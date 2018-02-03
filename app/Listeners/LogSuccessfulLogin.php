<?php

namespace App\Listeners;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
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
        if ($event->user->google2fa_secret != null) {
            $logEntry->event = 'auth.2fa_step';
        } else {
            $logEntry->event = 'auth.succeeded';
        }

        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();

        Log::error('LogSuccessfulLogin');
    }
}
