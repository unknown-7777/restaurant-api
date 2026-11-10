<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
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
        if ($order->status === 'paid') {
            return $this->error('Order is already paid.', 422);
        }

        if ($order->status === 'cancelled') {
            return $this->error('Cannot pay a cancelled order.', 422);
        }

        if ($request->amount < $order->total_amount) {
            return $this->error(
                'Amount paid is less than order total of ' . $order->total_amount . '.', 422
            );
        }

        DB::transaction(function () use ($request, $order) {
            $order->payment()->create([
                'processed_by'  => $request->user()->id,
                'method'        => $request->method,
                'amount'        => $request->amount,
                'change_amount' => $request->amount - $order->total_amount,
                'processed_at'  => now(),
            ]);

            $this->orderService->updateStatus($order, 'paid');
        });

        $order->load(['items.menuItem', 'items.modifiers', 'table', 'payment']);

        return $this->success(new OrderResource($order), 'Payment processed successfully.');
    }
}
