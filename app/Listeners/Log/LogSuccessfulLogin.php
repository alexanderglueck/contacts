<?php

namespace App\Listeners\Log;

use App\Models\LogEntry;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        $logEntry = new LogEntry();
        if ($event->user->hasTwoFactorAuthentication()) {
            $logEntry->event = 'auth.2fa_step';
        } else {
            $logEntry->event = 'auth.succeeded';
        }

        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
