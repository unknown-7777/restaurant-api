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
    ];

    protected function casts(): array
    {
        return ['bill_requested_at' => 'datetime'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
