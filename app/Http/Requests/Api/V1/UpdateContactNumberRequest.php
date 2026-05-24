<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit numbers');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'number' => ['sometimes', 'required', 'string', 'max:32', new ValidPhoneNumber],
        ];
    }
}
