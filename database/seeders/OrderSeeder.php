<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orderService = app(OrderService::class);
        $waiter = User::where('role', 'waiter')->first();
        $tables = Table::all();
        $menuItems = MenuItem::all();

        if ($tables->isEmpty() || $menuItems->isEmpty()) {
            $this->command->warn('No tables or menu items found. Run TableSeeder and MenuItemSeeder first.');
            return;
        }

        // Order 1 — pending
        $orderService->createOrder([
            'table_id' => $tables[0]->id,
            'user_id'  => $waiter->id,
            'notes'    => 'No onions please',
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Beef Burger')->first()->id,    'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Soft Drink')->first()->id,     'quantity' => 2],
            ],
        ]);

        // Order 2 — confirmed
        $order2 = $orderService->createOrder([
            'table_id' => $tables[1]->id,
            'user_id'  => $waiter->id,
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Caesar Salad')->first()->id,   'quantity' => 1],
                ['menu_item_id' => $menuItems->where('name', 'Grilled Salmon')->first()->id, 'quantity' => 1],
                ['menu_item_id' => $menuItems->where('name', 'Sparkling Water')->first()->id,'quantity' => 2],
            ],
        ]);
        $orderService->updateStatus($order2, 'confirmed');

        // Order 3 — preparing
        $order3 = $orderService->createOrder([
            'table_id' => $tables[2]->id,
            'user_id'  => $waiter->id,
            'notes'    => 'Birthday celebration',
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Chicken Wings')->first()->id,  'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Ribeye Steak')->first()->id,   'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Latte')->first()->id,          'quantity' => 2],
            ],
        ]);
        $orderService->updateStatus($order3, 'confirmed');
        $orderService->updateStatus($order3, 'preparing');

        // Order 4 — ready
        $order4 = $orderService->createOrder([
            'table_id' => $tables[3]->id,
            'user_id'  => $waiter->id,
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Spaghetti Bolognese')->first()->id, 'quantity' => 1],
                ['menu_item_id' => $menuItems->where('name', 'Garlic Bread')->first()->id,        'quantity' => 1],
                ['menu_item_id' => $menuItems->where('name', 'Fresh Orange Juice')->first()->id,  'quantity' => 1],
            ],
        ]);
        $orderService->updateStatus($order4, 'confirmed');
        $orderService->updateStatus($order4, 'preparing');
        $orderService->updateStatus($order4, 'ready');

        // Order 5 — served
        $order5 = $orderService->createOrder([
            'table_id' => $tables[4]->id,
            'user_id'  => $waiter->id,
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Spring Rolls')->first()->id,   'quantity' => 1],
                ['menu_item_id' => $menuItems->where('name', 'Fish & Chips')->first()->id,   'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Cheesecake')->first()->id,     'quantity' => 2],
            ],
        ]);
        $orderService->updateStatus($order5, 'confirmed');
        $orderService->updateStatus($order5, 'preparing');
        $orderService->updateStatus($order5, 'ready');
        $orderService->updateStatus($order5, 'served');

        // Order 6 — paid
        $order6 = $orderService->createOrder([
            'table_id' => $tables[5]->id,
            'user_id'  => $waiter->id,
            'items'    => [
                ['menu_item_id' => $menuItems->where('name', 'Greek Salad')->first()->id,    'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Grilled Chicken')->first()->id,'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Ice Cream')->first()->id,      'quantity' => 2],
                ['menu_item_id' => $menuItems->where('name', 'Cappuccino')->first()->id,     'quantity' => 2],
            ],
        ]);
        $orderService->updateStatus($order6, 'confirmed');
        $orderService->updateStatus($order6, 'preparing');
        $orderService->updateStatus($order6, 'ready');
        $orderService->updateStatus($order6, 'served');
        $cashier = User::where('role', 'cashier')->first();
        $order6->payment()->create([
            'processed_by'  => $cashier->id,
            'method'        => 'cash',
            'amount'        => 100.00,
            'change_amount' => 100.00 - $order6->total_amount,
            'processed_at'  => now(),
        ]);
        $orderService->updateStatus($order6, 'paid');

        $this->command->info('✓ Orders seeded — 6 demo orders across all statuses.');
    }
}
