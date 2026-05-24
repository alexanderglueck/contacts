<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the ?number=... query parameter for the by-number lookup
 * endpoint. The matching itself is done in the controller against a
 * digits-only-normalised version of the input.
 */
class LookupContactByNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:32'],
        ];
    }
}
