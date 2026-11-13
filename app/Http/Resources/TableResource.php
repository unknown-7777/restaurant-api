<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'number'     => $this->number,
            'capacity'   => $this->capacity,
            'status'     => $this->status,
            'qr_code'    => $this->qr_code,
            'bill_requested_at' => $this->bill_requested_at?->toDateTimeString(),
            'bill_requested_method' => $this->bill_requested_method,
            'qr_access_enabled' => (bool) $this->qr_access_enabled,
            'bill_total' => $this->relationLoaded('orders') ? number_format($this->orders->sum('total_amount'), 2, '.', '') : null,
            'bill_order_count' => $this->relationLoaded('orders') ? $this->orders->count() : null,
            'bill_ready' => $this->relationLoaded('orders')
                ? $this->orders->isNotEmpty() && $this->orders->every(fn ($order) => $order->status === 'served')
                : null,
            'qr_url'     => $this->qr_code && config('app.frontend_url')
                              ? rtrim(config('app.frontend_url'), '/') . '/order?table=' . urlencode($this->qr_code)
                              : null,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
