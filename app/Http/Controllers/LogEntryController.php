<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LogEntryController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Logs/Index', [
            'logs' => $request->user()->logs()->paginate(50)->through(fn ($log) => [
                'id' => $log->id,
                'event' => $log->event,
                'event_label' => trans('event.' . $log->event),
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at?->toDateTimeString(),
            ]),
        ]);
    }
}
