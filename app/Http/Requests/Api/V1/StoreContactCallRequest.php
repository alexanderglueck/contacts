<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create calls');
    }

    public function rules(): array
    {
        return [
            // ISO 8601: YYYY-MM-DDTHH:MM[:SS] (datetime-local) OR
            // YYYY-MM-DD HH:MM:SS. Model mutator accepts both.
            'called_at' => ['required', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
