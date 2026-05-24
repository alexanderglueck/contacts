<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\PaymentActionRequired;


class SubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $intent = $request->user()->createSetupIntent();

        return Inertia::render('Subscription/Index', [
            'plans' => Plan::active()->get()->map(fn ($plan) => [
                'gateway_id' => $plan->gateway_id,
                'slug' => $plan->slug,
                'name' => $plan->name,
                'price' => $plan->price,
            ])->all(),
            'intent' => [
                'client_secret' => $intent->client_secret,
            ],
            'stripeKey' => config('cashier.key'),
            'selectedPlan' => $request->query('plan'),
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
