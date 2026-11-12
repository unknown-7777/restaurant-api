<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'table_id'     => $this->table_id,
            'table'        => new TableResource($this->whenLoaded('table')),
            'user_id'      => $this->user_id,
            'status'       => $this->status,
            'total_amount' => $this->total_amount,
            'notes'        => $this->notes,
            'payment_requested_at' => $this->payment_requested_at?->toDateTimeString(),
            'payment_requested_method' => $this->payment_requested_method,
            'items'        => OrderItemResource::collection($this->whenLoaded('items')),
            'payment'      => new PaymentResource($this->whenLoaded('payment')),
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}
