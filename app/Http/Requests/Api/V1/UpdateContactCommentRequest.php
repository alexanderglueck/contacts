<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit comments');
    }

    public function rules(): array
    {
        return [
            'comment' => ['sometimes', 'required', 'string'],
        ];
    }
}
