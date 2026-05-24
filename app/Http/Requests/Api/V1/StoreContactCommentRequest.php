<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create comments');
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string'],
            // parent_ulid existence is verified against this contact's own
            // thread inside the controller — a plain exists:comments,ulid
            // would let a caller smuggle a parent from another contact.
            'parent_ulid' => ['nullable', 'string'],
        ];
    }
}
