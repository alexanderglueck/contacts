<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidIBANFormat;
use Illuminate\Foundation\Http\FormRequest;

/**
 * PATCH-style semantics: every field is optional, so the client can send
 * only the keys it wants to change. Validation runs only on keys that are
 * actually present (the `sometimes` rule on each).
 */
class ContactsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit contacts');
    }

    public function rules(): array
    {
        return [
            'salutation' => ['sometimes', 'required', 'string', 'max:255'],
            'firstname' => ['sometimes', 'required', 'string', 'max:255'],
            'lastname' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title_after' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nickname' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender_id' => ['sometimes', 'nullable', 'integer', 'exists:genders,id'],
            'company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'vatin' => ['sometimes', 'nullable', 'string', 'max:255'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job' => ['sometimes', 'nullable', 'string', 'max:255'],
            'custom_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'iban' => ['sometimes', 'nullable', new ValidIBANFormat],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'died_at' => ['sometimes', 'nullable', 'date'],
            'died_from' => ['sometimes', 'nullable', 'string', 'max:255'],
            'nationality_id' => ['sometimes', 'nullable', 'integer', 'exists:countries,id'],
            'first_met' => ['sometimes', 'nullable', 'string'],
            'note' => ['sometimes', 'nullable', 'string'],
            'active' => ['sometimes', 'boolean'],
            'contact_groups' => ['sometimes', 'array'],
            'contact_groups.*' => ['integer', 'exists:contact_groups,id'],
        ];
    }
}
