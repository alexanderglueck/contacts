<?php

namespace App\Http\Controllers\Teamwork;

use Exception;
use App\Models\Team;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Events\Tenant\TenantWasCreated;
use App\Http\Requests\Team\TeamStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        return view('teamwork.index', [
            'teams' => $request->user()->teams
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        return view('teamwork.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamStoreRequest $request): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $team = Team::create([
            'name' => $request->name,
            'owner_id' => $request->user()->getKey()
        ]);
        $request->user()->teams()->attach($team->id);

        event(new TenantWasCreated($team, $request->user()));

        return redirect()->route('teams.index');
    }

    /**
     * Switch to the given team.
     */
    public function switchTeam(Team $team): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        try {
            auth()->user()->switchTeam($team);

            session()->put('tenant', $team->uuid);
        } catch (Exception $e) {
            abort(403);
        }

        return redirect()->route('home');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team): View|RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        return view('teamwork.edit', [
            'team' => $team
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamStoreRequest $request, Team $team): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $team->name = $request->name;
        $team->save();

        return redirect(route('teams.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        /*
         * Set the team owner to null to prevent the foreign key restriction
         * from being applied
         */
        $team->update([
            'owner_id' => null
        ]);

        $team->delete();

        /*
         * Remove the team association to redirect them to the tenant selection
         * page
         */
        User::where('current_team_id', $team->id)->update([
            'current_team_id' => null
        ]);

        return redirect(route('teams.index'));
    }
}
