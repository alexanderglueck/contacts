<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRelationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit relations');
    }

    public function rules(): array
    {
        return [
            'forward_label' => ['required', 'string', 'max:255'],
            'inverse_label' => ['nullable', 'string', 'max:255'],
        ];
    }
}
