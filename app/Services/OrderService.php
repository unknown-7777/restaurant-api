<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    const TRANSITIONS = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready'     => ['served'],
        'served'    => ['paid'],
        'paid'      => [],
        'cancelled' => [],
    ];

    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'table_id'     => $data['table_id'] ?? null,
                'user_id'      => $data['user_id'] ?? null,
                'status'       => 'pending',
                'total_amount' => 0,
                'notes'        => $data['notes'] ?? null,
            ]);

            if (!empty($data['items'])) {
                $this->addItems($order, $data['items']);
            }

            return $order->fresh(['items.modifiers', 'table']);
        });
    }

    public function addItems(Order $order, array $items): void
    {
        foreach ($items as $itemData) {
            $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);

            $unitPrice = $menuItem->price;
            $modifierTotal = 0;

            $orderItem = $order->items()->create([
                'menu_item_id'   => $menuItem->id,
                'quantity'       => $itemData['quantity'],
                'unit_price'     => $unitPrice,
                'subtotal'       => $unitPrice * $itemData['quantity'],
                'special_request' => $itemData['special_request'] ?? null,
            ]);

            if (!empty($itemData['modifiers'])) {
                foreach ($itemData['modifiers'] as $modifierId) {
                    $modifier = $menuItem->modifiers()->findOrFail($modifierId);
                    $orderItem->modifiers()->create([
                        'modifier_id' => $modifier->id,
                        'name'        => $modifier->name,
                        'price_delta' => $modifier->price_delta,
                    ]);
                    $modifierTotal += $modifier->price_delta;
                }

                $orderItem->update([
                    'subtotal' => ($unitPrice + $modifierTotal) * $itemData['quantity'],
                ]);
            }
        }

        $this->recalculateTotal($order);
    }

    public function removeItem(Order $order, OrderItem $item): void
    {
        $item->modifiers()->delete();
        $item->delete();
        $this->recalculateTotal($order);
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $allowed = self::TRANSITIONS[$order->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            throw new \InvalidArgumentException(
                "Cannot transition order from '{$order->status}' to '{$newStatus}'."
            );
        }

        $order->update(['status' => $newStatus]);

        return $order->fresh(['items.modifiers', 'table', 'payment']);
    }

    public function recalculateTotal(Order $order): void
    {
        $total = $order->items()->sum('subtotal');
        $order->update(['total_amount' => $total]);
    }
}
