<?php

namespace App\Rules;

use Exception;
use Stripe\Coupon;
use Illuminate\Contracts\Validation\Rule;

class ValidStripeCoupon implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            Coupon::retrieve($value);
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The coupon is invalid.';
    }
}
