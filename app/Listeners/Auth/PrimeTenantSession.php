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

        Session::put('tenant', $user->currentTeam->uuid);
    }
}
