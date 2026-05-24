<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionCancelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('UserSettings/Subscription/Cancel');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->subscription('main')->cancel();

        flashSuccess('Your subscription has been cancelled.');

        return redirect()->route('user_settings.profile.show');
    }
}
