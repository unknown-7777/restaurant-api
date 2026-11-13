<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrGuestSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'table_id',
        'token_hash',
        'expires_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'qr_guest_session_id');
    }
}
