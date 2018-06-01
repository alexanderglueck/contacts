<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionResumeController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.resume.index');
    }

    public function store(Request $request)
    {
        $request->user()->subscription('main')->resume();

        flashSuccess('Your subscription has been resumed.');

        return redirect()->route('user_settings.profile.show');
    }
}
