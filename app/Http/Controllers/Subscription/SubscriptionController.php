<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\PaymentActionRequired;


class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $plans = Plan::active()->get();

        return view('subscription.index', [
            'plans' => $plans,
            'intent' => $request->user()->createSetupIntent()
        ]);
    }

    public function store(SubscriptionStoreRequest $request): RedirectResponse
    {
        $subscription = $request->user()
            ->newSubscription('main', $request->plan);

        if ($request->has('coupon')) {
            $subscription->withCoupon($request->coupon);
        }

        try {
            $subscription->create($request->token);
        } catch (PaymentActionRequired $exception) {
            return redirect()->route('cashier.payment',
                [$exception->payment->id, 'redirect' => route('home')]
            );
        }

        flashSuccess('Thanks for becoming a subscriber!');

        return redirect()->route('home');
    }
}
