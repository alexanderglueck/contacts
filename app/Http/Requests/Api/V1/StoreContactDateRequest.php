<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create dates');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'skip_year' => ['sometimes', 'boolean'],
        ];
    }
}
