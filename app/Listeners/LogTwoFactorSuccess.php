<?php

namespace App\Listeners;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Request;

class LogTwoFactorSuccess
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $logEntry = new LogEntry();
        $logEntry->event = 'auth.2fa_succeeded';
        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
