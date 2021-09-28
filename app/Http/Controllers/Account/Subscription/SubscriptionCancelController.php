<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionCancelController extends Controller
{
    public function index(): View
    {
        return view('user_settings.subscription.cancel.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->subscription('main')->cancel();

        flashSuccess('Your subscription has been cancelled.');

        return redirect()->route('user_settings.profile.show');
    }
}
