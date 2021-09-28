<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Tenant\TenantSelectionStoreRequest;

class TenantSelectionController extends Controller
{
    /**
     * Display a list of tenants for the user to switch to
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        return view('tenant.index', [
            'teams' => $request->user()->teams
        ]);
    }

    /**
     * Switch the currently authenticated user to the selected tenant
     *
     * @param TenantSelectionStoreRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TenantSelectionStoreRequest $request): RedirectResponse
    {
        $team = Team::find($request->team);

        $request->user()->switchTeam($team);

        session()->put('tenant', $team->uuid);

        return redirect()->route('home');
    }
}
