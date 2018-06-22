<?php

namespace App\Listeners\Log;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Request;
use App\Events\TwoFactor\TwoFactorSuccess;

class LogTwoFactorSuccess
{
    /**
     * Handle the event.
     *
     * @param  TwoFactorSuccess $event
     *
     * @return void
     */
    public function handle(TwoFactorSuccess $event)
    {
        $logEntry = new LogEntry();
        $logEntry->event = 'auth.2fa_succeeded';
        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
