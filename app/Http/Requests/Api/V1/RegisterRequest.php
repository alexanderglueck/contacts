<?php

namespace App\Http\Requests\Api\V1;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Mirrors the rules in App\Actions\Fortify\CreateNewUser. The action still
 * re-validates internally so the web Fortify route (which doesn't go
 * through a FormRequest) stays safe — this class exists primarily so
 * Scramble can introspect the request body for the OpenAPI spec, and so
 * 422 responses come back with field-scoped errors the Android client can
 * map directly to form fields.
 */
class RegisterRequest extends FormRequest
{
    use PasswordValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Note: `terms` uses required+boolean+accepted instead of just
        // `accepted` so Scramble emits a clean `boolean` schema (and
        // includes it in `required`) rather than `string`.
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'terms' => ['required', 'boolean', 'accepted'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
