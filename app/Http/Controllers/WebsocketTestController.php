<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\WebsocketTest;

class WebsocketTestController extends Controller
{
    public function index(Request $request)
    {
        event(new WebsocketTest(now()->format('d.m.Y H:i:s')));

        return 'done';
    }
}
