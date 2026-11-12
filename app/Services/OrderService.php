<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
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

            $this->syncTableStatus($order->table_id);

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
        $this->syncTableStatus($order->table_id);
    }

    public function removeItem(Order $order, OrderItem $item): void
    {
        $item->modifiers()->delete();
        $item->delete();
        $this->recalculateTotal($order);
        $this->syncTableStatus($order->table_id);
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

        if (in_array($newStatus, ['paid', 'cancelled'], true)) {
            $this->syncTableStatus($order->table_id);
        }

        return $order->fresh(['items.modifiers', 'table', 'payment']);
    }

    private function syncTableStatus(?string $tableId): void
    {
        if (!$tableId) {
            return;
        }

        $hasOpenOrders = Order::where('table_id', $tableId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
            ->exists();

        Table::whereKey($tableId)->update([
            'status' => $hasOpenOrders ? 'occupied' : 'available',
        ]);
    }

    public function recalculateTotal(Order $order): void
    {
        $total = $order->items()->sum('subtotal');
        $order->update(['total_amount' => $total]);
    }
}
