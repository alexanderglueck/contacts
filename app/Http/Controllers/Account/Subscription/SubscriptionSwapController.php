<?php

namespace App\Http\Controllers\Account\Subscription;

use App\Models\Plan;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\SubscriptionSwapStoreRequest;

class SubscriptionSwapController extends Controller
{
    public function index()
    {
        $plans = Plan::except(auth()->user()->plan->id)->active()->get();

        return view('user_settings.subscription.swap.index', [
            'plans' => $plans
        ]);
    }

    public function store(SubscriptionSwapStoreRequest $request)
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

        $user->subscription('main')->swap($plan->gateway_id);

        flashSuccess('Your plan has been changed!');

        return back();
    }

    protected function downgradesFromTeamPlan(User $user, Plan $plan)
    {
        return $user->plan->isForTeams() && $plan->isNotForTeams();
    }
}
