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
        $rules = [
            'status'   => ['sometimes', 'in:available,occupied,reserved'],
            'qr_access_enabled' => ['sometimes', 'boolean'],
        ];

        if ($this->user()?->role !== 'waiter') {
            $rules['capacity'] = ['sometimes', 'integer', 'min:1'];
        }

        return $rules;
    }
}
