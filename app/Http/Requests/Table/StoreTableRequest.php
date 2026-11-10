<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number'   => ['required', 'integer', 'unique:tables,number'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
