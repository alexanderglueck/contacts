<?php

namespace App\Http\Controllers;

use App\Events\WebsocketTest;
use Illuminate\Http\Request;

class WebsocketTestController extends Controller
{
    public function index(Request $request)
    {
        event(new WebsocketTest(now()->format('d.m.Y H:i:s')));

        return "done";
    }
}
