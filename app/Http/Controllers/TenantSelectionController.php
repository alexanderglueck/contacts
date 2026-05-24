<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Tenant\TenantSelectionStoreRequest;

class TenantSelectionController extends Controller
{
    /**
     * Display a list of tenants for the user to switch to
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Tenant/Index', [
            'teams' => $request->user()->teams->map(fn ($team) => [
                'id' => $team->id,
                'name' => $team->name,
            ])->all(),
        ]);
    }

    /**
     * Switch the currently authenticated user to the selected tenant
     */
    public function store(TenantSelectionStoreRequest $request): RedirectResponse
    {
        $team = Team::find($request->team);

        $request->user()->switchTeam($team);

        session()->put('tenant', $team->uuid);

        return redirect()->route('home');
    }
}
