<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * Validate a phone number using libphonenumber (giggsey/libphonenumber-for-php-lite).
 *
 * Numbers in international format (e.g. "+43 1 234 5678") validate against
 * the appropriate country automatically. Numbers in national format
 * (e.g. "0664 123 4567") need a default region to disambiguate — that
 * comes from config('contacts.phone_default_region'), default AT.
 *
 * Note: this only validates the input shape. Normalisation to E.164 for
 * consistent storage is a separate concern handled (later, optionally) by
 * a mutator on ContactNumber.
 */
class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            $fail('The :attribute must be a phone number.');

            return;
        }

        $region = config('contacts.phone_default_region', 'AT');

        try {
            $parsed = PhoneNumberUtil::getInstance()->parse($value, $region);
        } catch (NumberParseException) {
            $fail('The :attribute is not a recognisable phone number.');

            return;
        }

        if (! PhoneNumberUtil::getInstance()->isValidNumber($parsed)) {
            $fail('The :attribute is not a valid phone number.');
        }
    }
}
