<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'                    => ['nullable', 'uuid', 'exists:tables,id'],
            'notes'                       => ['nullable', 'string'],
            'items'                       => ['nullable', 'array'],
            'items.*.menu_item_id'        => ['required_with:items', 'uuid', 'exists:menu_items,id'],
            'items.*.quantity'            => ['required_with:items', 'integer', 'min:1'],
            'items.*.special_request'     => ['nullable', 'string'],
            'items.*.modifiers'           => ['nullable', 'array'],
            'items.*.modifiers.*'         => ['uuid', 'exists:modifiers,id'],
        ];
    }
}
