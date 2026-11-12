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
            'image'        => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_available' => ['sometimes', 'boolean'],
            'sort_order'   => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('sort_order') && $this->input('sort_order') === '') {
            $this->merge(['sort_order' => null]);
        }
        if ($this->has('is_available')) {
            $this->merge([
                'is_available' => filter_var($this->is_available, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);
        }
    }
}
