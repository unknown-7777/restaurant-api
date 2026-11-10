<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'order_id'      => $this->order_id,
            'method'        => $this->method,
            'amount'        => $this->amount,
            'change_amount' => $this->change_amount,
            'processed_by'  => $this->processed_by,
            'processed_at'  => $this->processed_at?->toDateTimeString(),
        ];
    }
}
