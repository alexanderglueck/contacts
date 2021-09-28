<?php

namespace App\Http\Controllers;

use App\Events\WebsocketTest;

class WebsocketTestController extends Controller
{
    public function index(): string
    {
        event(new WebsocketTest(now()->format('d.m.Y H:i:s')));

        return 'done';
    }
}
