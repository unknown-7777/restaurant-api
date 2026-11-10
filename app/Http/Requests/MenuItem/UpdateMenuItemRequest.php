<?php

namespace App\Http\Requests\MenuItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'  => ['sometimes', 'uuid', 'exists:categories,id'],
            'name'         => ['sometimes', 'string', 'max:255'],
            'description'  => ['sometimes', 'nullable', 'string'],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'image'        => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_available' => ['sometimes', 'boolean'],
            'sort_order'   => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
