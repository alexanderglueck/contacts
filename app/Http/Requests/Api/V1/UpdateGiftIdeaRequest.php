<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit giftIdeas');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'due_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
