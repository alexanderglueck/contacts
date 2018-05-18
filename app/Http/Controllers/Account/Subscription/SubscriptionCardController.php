<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionCardController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.card.index');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $request->user()->updateCard($request->token);

        flashSuccess('Your card has been updated!');

        return redirect()->route('user_settings.subscription.card.index');
    }
}
