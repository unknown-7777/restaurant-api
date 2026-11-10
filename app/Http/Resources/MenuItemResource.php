<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'category_id'  => $this->category_id,
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'name'         => $this->name,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'price'        => $this->price,
            'image_url'    => $this->image_path
                                ? asset('storage/' . $this->image_path)
                                : null,
            'is_available' => $this->is_available,
            'sort_order'   => $this->sort_order,
            'modifiers'    => ModifierResource::collection($this->whenLoaded('modifiers')),
            'created_at'   => $this->created_at->toDateTimeString(),
        ];
    }
}
