<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\StoreTablePaymentRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Table;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(protected OrderService $orderService) {}

    public function store(StorePaymentRequest $request, Order $order): JsonResponse
    {
        $result = DB::transaction(function () use ($request, $order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->payment()->exists() || $order->status === 'paid') {
                return 'Order is already paid.';
            }
            if ($order->status !== 'served') {
                return 'Payment can only be processed after the order is served.';
            }
            if ($request->amount < $order->total_amount) {
                return 'Amount paid is less than order total of ' . $order->total_amount . '.';
            }
            if ($request->method !== 'cash' && (float) $request->amount !== (float) $order->total_amount) {
                return 'Card and QRIS payments must match the order total exactly.';
            }

            $order->payment()->create([
                'processed_by'  => $request->user()->id,
                'method'        => $request->method,
                'amount'        => $request->amount,
                'change_amount' => $request->amount - $order->total_amount,
                'processed_at'  => now(),
            ]);

            $this->orderService->updateStatus($order, 'paid');

            return null;
        });

        if ($result !== null) {
            return $this->error($result, 422);
        }

        $order->load(['items.menuItem', 'items.modifiers', 'table', 'payment']);

        return $this->success(new OrderResource($order), 'Payment processed successfully.');
    }

    public function storeForTable(StoreTablePaymentRequest $request, Table $table): JsonResponse
    {
        $result = DB::transaction(function () use ($request, $table) {
            $table = Table::whereKey($table->id)->lockForUpdate()->firstOrFail();
            if (!$table->bill_requested_at) {
                return ['error' => 'The customer has not requested the table bill.'];
            }
            if ($request->method !== $table->bill_requested_method) {
                return ['error' => 'Use the payment method selected by the customer: ' . strtoupper($table->bill_requested_method) . '.'];
            }

            $unpaidOrders = Order::where('table_id', $table->id)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                ->whereDoesntHave('payment')
                ->lockForUpdate()
                ->get();

            if ($unpaidOrders->isEmpty()) {
                return ['error' => 'There are no unpaid orders for this table.'];
            }
            if ($unpaidOrders->contains(fn ($order) => $order->status !== 'served')) {
                return ['error' => 'All table orders must be served before one payment can be processed.'];
            }

            $total = round((float) $unpaidOrders->sum('total_amount'), 2);
            $tendered = round((float) $request->amount, 2);
            if ($tendered < $total) {
                return ['error' => 'Amount paid is less than the table total of ' . number_format($total, 2) . '.'];
            }
            if ($request->method !== 'cash' && $tendered !== $total) {
                return ['error' => 'Card and QRIS payments must match the table total exactly.'];
            }

            $change = round($tendered - $total, 2);
            $lastOrderId = $unpaidOrders->last()->id;
            foreach ($unpaidOrders as $order) {
                $orderAmount = round((float) $order->total_amount, 2);
                $orderChange = $order->id === $lastOrderId ? $change : 0;
                $order->payment()->create([
                    'processed_by' => $request->user()->id,
                    'method' => $request->method,
                    'amount' => $orderAmount + $orderChange,
                    'change_amount' => $orderChange,
                    'processed_at' => now(),
                ]);
                $this->orderService->updateStatus($order, 'paid');
            }

            $table->update([
                'bill_requested_at' => null,
                'bill_requested_method' => null,
            ]);

            return [
                'orders' => $unpaidOrders->pluck('id')->all(),
                'total' => $total,
                'change' => $change,
            ];
        });

        if (isset($result['error'])) {
            return $this->error($result['error'], 422);
        }

        $orders = Order::whereIn('id', $result['orders'])
            ->with(['table', 'items.menuItem', 'items.modifiers', 'payment'])
            ->latest()
            ->get();

        return $this->success([
            'orders' => OrderResource::collection($orders),
            'total' => number_format($result['total'], 2, '.', ''),
            'change' => number_format($result['change'], 2, '.', ''),
        ], 'Table payment processed successfully.');
    }
}
