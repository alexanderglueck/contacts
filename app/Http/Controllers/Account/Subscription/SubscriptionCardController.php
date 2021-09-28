<?php

namespace App\Http\Controllers\Account\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\SubscriptionCardStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionCardController extends Controller
{
    public function index(Request $request): View
    {
        return view('user_settings.subscription.card.index', [
            'intent' => $request->user()->createSetupIntent()
        ]);
    }

    public function store(SubscriptionCardStoreRequest $request): RedirectResponse
    {
        $request->user()->updateDefaultPaymentMethod($request->token);

        flashSuccess('Your card has been updated!');

        return redirect()->route('user_settings.subscription.card.index');
    }
}
