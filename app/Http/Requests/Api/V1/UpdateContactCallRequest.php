<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit calls');
    }

    public function rules(): array
    {
        return [
            'called_at' => ['sometimes', 'required', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
