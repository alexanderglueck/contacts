<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create notes');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'note' => ['required', 'string'],
        ];
    }
}
