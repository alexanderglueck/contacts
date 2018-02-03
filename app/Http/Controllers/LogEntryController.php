<?php

namespace App\Http\Controllers;

use Auth;

class LogEntryController extends Controller
{
    public function index()
    {
        return view('log_entry.index', [
            'logs' => Auth::user()->logs()->paginate(50)
        ]);
    }
}
