<?php

namespace App\Listeners;

use App\Models\LogEntry;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
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
        if ( ! isset($event->user->id)) {
            return;
        }

        $logEntry = new LogEntry();
        $logEntry->created_by = $event->user->id;
        $logEntry->updated_by = $event->user->id;
        $logEntry->event = 'auth.failed';
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
