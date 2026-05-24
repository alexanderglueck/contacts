<?php

namespace App\Http\Controllers\Account\Subscription;

use App\Models\Plan;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionSwapStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\PaymentActionRequired;

class SubscriptionSwapController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $plans = Plan::where('id', '!=', $user->plan->id)->active()->get();

        return Inertia::render('UserSettings/Subscription/Swap', [
            'plans' => $plans->map(fn ($plan) => [
                'gateway_id' => $plan->gateway_id,
                'name' => $plan->name,
                'price' => $plan->price,
            ])->all(),
            'currentPlan' => [
                'name' => $user->plan->name,
                'price' => $user->plan->price,
            ],
        ]);
    }

    public function store(SubscriptionSwapStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        $plan = Plan::where('gateway_id', $request->plan)->first();

        if ($this->downgradesFromTeamPlan($user, $plan)) {
            $user->currentTeam->users()->each(function ($member) use ($user) {
                if ($member->id == $user->id) {
                    return;
                }

                $member->update([
                    'current_team_id' => $member->teams->except($user->currentTeam->id)->first()->id
                ]);
            });

            $user->currentTeam->users()->sync([$user->id]);
        }

        try {
            $user->subscription('main')->swap($plan->gateway_id);
        } catch (PaymentActionRequired $exception) {
            return redirect()->route('cashier.payment',
                [$exception->payment->id, 'redirect' => route('home')]
            );
        }

        flashSuccess('Your plan has been changed!');

        return back();
    }

    protected function downgradesFromTeamPlan(User $user, Plan $plan): bool
    {
        return $user->plan->isForTeams() && $plan->isNotForTeams();
    }
}
