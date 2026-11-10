<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModifierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'menu_item_id' => $this->menu_item_id,
            'name'         => $this->name,
            'type'         => $this->type,
            'price_delta'  => $this->price_delta,
            'is_available' => $this->is_available,
            'created_at'   => $this->created_at->toDateTimeString(),
        ];
    }
}
