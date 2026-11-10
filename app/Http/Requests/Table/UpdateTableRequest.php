<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'   => ['sometimes', 'in:available,occupied,reserved'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
