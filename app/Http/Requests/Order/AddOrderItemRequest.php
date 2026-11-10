<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_item_id'    => ['required', 'uuid', 'exists:menu_items,id'],
            'quantity'        => ['required', 'integer', 'min:1'],
            'special_request' => ['nullable', 'string'],
            'modifiers'       => ['nullable', 'array'],
            'modifiers.*'     => ['uuid', 'exists:modifiers,id'],
        ];
    }
}
