<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class PrimeTenantSession
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! method_exists($user, 'currentTeam') || $user->currentTeam === null) {
            return;
        }

        // The same Login event fires for token-authed API requests (mobile),
        // where no session middleware ran. Skip the write rather than throw —
        // those clients pass tenant context explicitly per request.
        if (! Session::isStarted()) {
            return;
        }

        Session::put('tenant', $user->currentTeam->uuid);
    }
}
