<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create urls');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
        ];
    }
}
