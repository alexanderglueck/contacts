<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

class SeatBilling
{
    /**
     * Whether team seat-quantity changes should be synced to Stripe.
     *
     * Team membership (invite-accept / member-removal) adjusts the owner's
     * Stripe subscription quantity. Outside production, when no Stripe secret
     * is configured, those calls would throw ("api_key cannot be the empty
     * string") and make the flows unusable locally — so we skip the sync. In
     * production a key is always present, so seats always sync.
     */
    public static function syncsToStripe(): bool
    {
        return App::isProduction() || ! empty(config('cashier.secret'));
    }
}
