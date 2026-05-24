<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    public function index(): Response
    {
        $mapPlan = fn ($plan) => [
            'id' => $plan->id,
            'slug' => $plan->slug,
            'name' => $plan->name,
            'price' => $plan->price,
            'teams_limit' => $plan->teams_limit,
        ];

        return Inertia::render('Plans/Index', [
            'plans' => Plan::active()->forUsers()->get()->map($mapPlan)->all(),
            'teamPlans' => Plan::active()->forTeams()->get()->map($mapPlan)->all(),
        ]);
    }
}
