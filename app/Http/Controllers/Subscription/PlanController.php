<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->forUsers()->get();
        $teamPlans = Plan::active()->forTeams()->get();

        return view('subscription.plans.index', [
            'plans' => $plans,
            'teamPlans' => $teamPlans
        ]);
    }
}
