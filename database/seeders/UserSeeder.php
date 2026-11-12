<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin User',
                'email'     => 'admin@restaurant.com',
                'password'  => 'password',
                'role'      => 'admin',
                'is_active' => true,
            ],
            [
                'name'      => 'Manager Sarah',
                'email'     => 'manager@restaurant.com',
                'password'  => 'password',
                'role'      => 'manager',
                'is_active' => true,
            ],
            [
                'name'      => 'Waiter John',
                'email'     => 'waiter@restaurant.com',
                'password'  => 'password',
                'role'      => 'waiter',
                'is_active' => true,
            ],
            [
                'name'      => 'Cashier Mike',
                'email'     => 'cashier@restaurant.com',
                'password'  => 'password',
                'role'      => 'cashier',
                'is_active' => true,
            ],
            [
                'name'      => 'Chef David',
                'email'     => 'kitchen@restaurant.com',
                'password'  => 'password',
                'role'      => 'kitchen',
                'is_active' => true,
            ],
            [
                'name'      => 'Customer Alice',
                'email'     => 'customer@restaurant.com',
                'password'  => 'password',
                'role'      => 'customer',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✓ Users seeded — 6 accounts created.');
    }
}
