<?php

namespace App\Http\Requests\Modifier;

use Illuminate\Foundation\Http\FormRequest;

class StoreModifierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', 'in:single,multiple'],
            'price_delta'  => ['nullable', 'numeric'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }
}
