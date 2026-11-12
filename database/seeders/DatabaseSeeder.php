<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Restaurant Demo Data...');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            MenuItemSeeder::class,
            TableSeeder::class,
            OrderSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',    'admin@restaurant.com',    'password'],
                ['Manager',  'manager@restaurant.com',  'password'],
                ['Waiter',   'waiter@restaurant.com',   'password'],
                ['Cashier',  'cashier@restaurant.com',  'password'],
                ['Kitchen',  'kitchen@restaurant.com',  'password'],
                ['Customer', 'customer@restaurant.com', 'password'],
            ]
        );
    }
}
