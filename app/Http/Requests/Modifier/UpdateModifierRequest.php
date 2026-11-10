<?php

namespace App\Http\Requests\Modifier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModifierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'type'         => ['sometimes', 'in:single,multiple'],
            'price_delta'  => ['sometimes', 'numeric'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }
}
