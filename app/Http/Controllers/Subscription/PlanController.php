<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::active()->forUsers()->get();
        $teamPlans = Plan::active()->forTeams()->get();

        return view('subscription.plans.index', [
            'plans' => $plans,
            'teamPlans' => $teamPlans
        ]);
    }
}
