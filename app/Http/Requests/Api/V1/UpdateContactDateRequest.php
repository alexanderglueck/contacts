<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit dates');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'date' => ['sometimes', 'required', 'date'],
            'skip_year' => ['sometimes', 'boolean'],
        ];
    }
}
