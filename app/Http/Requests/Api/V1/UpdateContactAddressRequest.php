<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit addresses');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'street' => ['sometimes', 'required', 'string', 'max:255'],
            'zip' => ['sometimes', 'required', 'string', 'max:32'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country_id' => ['sometimes', 'required', 'integer', 'exists:countries,id'],
        ];
    }
}
