<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modifier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'menu_item_id',
        'name',
        'type',
        'price_delta',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
