<?php

namespace App\Listeners\Log;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Request;
use App\Events\TwoFactor\TwoFactorFailure;

class LogTwoFactorFailure
{
    /**
     * Handle the event.
     *
     * @param  TwoFactorFailure $event
     *
     * @return void
     */
    public function handle(TwoFactorFailure $event)
    {
        $logEntry = new LogEntry();
        $logEntry->event = 'auth.2fa_failed';
        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
