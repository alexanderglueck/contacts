<?php

namespace App\Http\Controllers\Account\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\SubscriptionCardStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionCardController extends Controller
{
    public function index(Request $request): Response
    {
        $intent = $request->user()->createSetupIntent();

        return Inertia::render('UserSettings/Subscription/Card', [
            'intent' => [
                'client_secret' => $intent->client_secret,
            ],
            'stripeKey' => config('cashier.key'),
        ]);
    }

    public function store(SubscriptionCardStoreRequest $request): RedirectResponse
    {
        $request->user()->updateDefaultPaymentMethod($request->token);

        flashSuccess('Your card has been updated!');

        return redirect()->route('user_settings.subscription.card.index');
    }
}
