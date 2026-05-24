<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit notes');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'note' => ['sometimes', 'required', 'string'],
        ];
    }
}
