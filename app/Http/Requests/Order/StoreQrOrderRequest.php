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
            'guest_session_token'     => ['required', 'string', 'size:64'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
            'items'                   => ['required', 'array', 'min:1', 'max:20'],
            'items.*.menu_item_id'    => [
                'required',
                'uuid',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('is_available', true)),
            ],
            'items.*.quantity'        => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.special_request' => ['nullable', 'string', 'max:500'],
            'items.*.modifiers'       => ['nullable', 'array', 'max:10'],
            'items.*.modifiers.*'     => [
                'uuid',
                Rule::exists('modifiers', 'id')->where(fn ($query) => $query->where('is_available', true)),
            ],
        ];
    }
}
