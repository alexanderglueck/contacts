<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionStoreRequest;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->get();

        return view('subscription.index', [
            'plans' => $plans
        ]);
    }

    public function store(SubscriptionStoreRequest $request)
    {
        $subscription = $request->user()
            ->newSubscription('main', $request->plan);

        if ($request->has('coupon')) {
            $subscription->withCoupon($request->coupon);
        }

        $subscription->create($request->token);

        flashSuccess('Thanks for becoming a subscriber!');

        return redirect()->route('home');
    }
}
