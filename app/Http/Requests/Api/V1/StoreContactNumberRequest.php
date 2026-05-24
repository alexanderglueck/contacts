<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create numbers');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:32', new ValidPhoneNumber],
        ];
    }
}
