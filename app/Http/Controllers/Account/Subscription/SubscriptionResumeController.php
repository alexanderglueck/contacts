<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionResumeController extends Controller
{
    public function index(): View
    {
        return view('user_settings.subscription.resume.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->subscription('main')->resume();

        flashSuccess('Your subscription has been resumed.');

        return redirect()->route('user_settings.profile.show');
    }
}
