<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'number',
        'capacity',
        'status',
        'qr_code',
        'bill_requested_at',
        'bill_requested_method',
        'qr_access_enabled',
    ];

    protected function casts(): array
    {
        return [
            'bill_requested_at' => 'datetime',
            'qr_access_enabled' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
