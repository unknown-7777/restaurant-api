<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AddOrderItemRequest;
use App\Http\Requests\Order\StoreQrOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['table', 'items.menuItem', 'items.modifiers'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return $this->success([
            'orders' => OrderResource::collection($orders),
            'meta'   => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder([
            'table_id' => $request->table_id,
            'user_id'  => $request->user()->id,
            'notes'    => $request->notes,
            'items'    => $request->items ?? [],
        ]);

        return $this->success(new OrderResource($order), 'Order created.', 201);
    }

    public function storeFromQr(StoreQrOrderRequest $request): JsonResponse
    {
        $table = \App\Models\Table::where('qr_code', $request->table_qr_code)->firstOrFail();
        if ($table->bill_requested_at) {
            return $this->error('The table bill has been requested. Please wait for staff to finish payment before placing another order.', 422);
        }

        $order = $this->orderService->createOrder([
            'table_id' => $table->id,
            'user_id'  => null,
            'notes'    => $request->notes,
            'items'    => $request->items,
        ]);

        return $this->success(new OrderResource($order), 'QR order created.', 201);
    }

    public function showManyFromQr(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'order_ids' => ['required', 'array', 'min:1', 'max:10'],
            'order_ids.*' => ['required', 'uuid'],
        ]);

        $orders = Order::whereIn('id', $validated['order_ids'])
            ->whereHas('table', fn ($query) => $query->where('qr_code', $validated['table_qr_code']))
            ->with(['table', 'items.menuItem', 'items.modifiers', 'payment'])
            ->latest()
            ->get();

        return $this->success(OrderResource::collection($orders));
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['table', 'items.menuItem', 'items.modifiers', 'payment']);

        return $this->success(new OrderResource($order));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            $order = $this->orderService->updateStatus($order, $request->status);
            return $this->success(new OrderResource($order), 'Order status updated.');
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function addItem(AddOrderItemRequest $request, Order $order): JsonResponse
    {
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return $this->error('Cannot add items to a ' . $order->status . ' order.', 422);
        }

        $this->orderService->addItems($order, [$request->validated()]);
        $order->load(['items.menuItem', 'items.modifiers', 'table']);

        return $this->success(new OrderResource($order), 'Item added to order.');
    }

    public function removeItem(Order $order, OrderItem $item): JsonResponse
    {
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return $this->error('Cannot remove items from a ' . $order->status . ' order.', 422);
        }

        $this->orderService->removeItem($order, $item);
        $order->load(['items.menuItem', 'items.modifiers', 'table']);

        return $this->success(new OrderResource($order), 'Item removed from order.');
    }
}
