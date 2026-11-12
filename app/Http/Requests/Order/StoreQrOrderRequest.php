<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQrOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_qr_code'           => ['required', 'uuid', 'exists:tables,qr_code'],
            'notes'                   => ['nullable', 'string'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.menu_item_id'    => [
                'required',
                'uuid',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('is_available', true)),
            ],
            'items.*.quantity'        => ['required', 'integer', 'min:1'],
            'items.*.special_request' => ['nullable', 'string'],
            'items.*.modifiers'       => ['nullable', 'array'],
            'items.*.modifiers.*'     => ['uuid', 'exists:modifiers,id'],
        ];
    }
}
