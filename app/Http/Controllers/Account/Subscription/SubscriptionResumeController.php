<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionResumeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('UserSettings/Subscription/Resume');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->subscription('main')->resume();

        flashSuccess('Your subscription has been resumed.');

        return redirect()->route('user_settings.profile.show');
    }
}
