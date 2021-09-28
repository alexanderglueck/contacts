<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogEntryController extends Controller
{
    public function index(Request $request): View
    {
        return view('log_entry.index', [
            'logs' => $request->user()->logs()->paginate(50)
        ]);
    }
}
