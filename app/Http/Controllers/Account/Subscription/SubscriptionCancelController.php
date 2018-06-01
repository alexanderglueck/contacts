<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionCancelController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.cancel.index');
    }

    public function store(Request $request)
    {
        $request->user()->subscription('main')->cancel();

        flashSuccess('Your subscription has been cancelled.');

        return redirect()->route('user_settings.profile.show');
    }
}
