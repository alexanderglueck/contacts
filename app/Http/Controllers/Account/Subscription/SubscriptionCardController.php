<?php

namespace App\Http\Controllers\Account\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\SubscriptionCardStoreRequest;

class SubscriptionCardController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.card.index');
    }

    public function store(SubscriptionCardStoreRequest $request)
    {
        $request->user()->updateCard($request->token);

        flashSuccess('Your card has been updated!');

        return redirect()->route('user_settings.subscription.card.index');
    }
}
