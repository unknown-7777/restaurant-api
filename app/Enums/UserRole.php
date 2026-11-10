<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin    = 'admin';
    case Manager  = 'manager';
    case Waiter   = 'waiter';
    case Cashier  = 'cashier';
    case Kitchen  = 'kitchen';
    case Customer = 'customer';

    public function label(): string
    {
        return match($this) {
            self::Admin    => 'Administrator',
            self::Manager  => 'Manager',
            self::Waiter   => 'Waiter',
            self::Cashier  => 'Cashier',
            self::Kitchen  => 'Kitchen Staff',
            self::Customer => 'Customer',
        };
    }
}
