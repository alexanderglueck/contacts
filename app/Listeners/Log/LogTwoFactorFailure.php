<?php

namespace App\Listeners\Log;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Request;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;

class LogTwoFactorFailure
{
    public function handle(TwoFactorAuthenticationFailed $event): void
    {
        $logEntry = new LogEntry();
        $logEntry->event = 'auth.2fa_failed';
        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
