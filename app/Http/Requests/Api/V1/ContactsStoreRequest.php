<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ValidIBANFormat;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Looser than the web ContactStoreRequest on two axes the API actually
 * needs:
 *   - No `present` rules. Mobile clients send only the fields they care
 *     about; they shouldn't have to include `title: ""` in the payload
 *     just to satisfy the validator.
 *   - `date` instead of `date_format:d.m.Y`. ISO dates are what JSON
 *     clients (and our own <input type="date">) actually send.
 */
class ContactsStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create contacts');
    }

    public function rules(): array
    {
        return [
            'salutation' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
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
