<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionSwapController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.swap.index');
    }

    public function store(Request $request)
    {
    }
}
