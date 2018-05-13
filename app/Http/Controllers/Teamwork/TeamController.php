<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Tenant\TenantWasCreated;
use Spatie\Permission\PermissionRegistrar;
use Mpociot\Teamwork\Exceptions\UserNotInTeamException;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        return view('teamwork.index')
            ->with('teams', auth()->user()->teams);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        return view('teamwork.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');

        $team = $teamModel::create([
            'name' => $request->name,
            'owner_id' => $request->user()->getKey()
        ]);
        $request->user()->attachTeam($team);

        event(new TenantWasCreated($team));

        return redirect()->route('teams.switch', [$team->id]);
    }

    /**
     * Switch to the given team.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function switchTeam($id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        try {
            auth()->user()->switchTeam($team);

            session()->put('tenant', $team->uuid);
        } catch (UserNotInTeamException $e) {
            abort(403);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        return view('teamwork.edit')->withTeam($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);
        $team->name = $request->name;
        $team->save();

        return redirect(route('teams.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');

        $team = $teamModel::findOrFail($id);
        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $team->delete();

        $userModel = config('teamwork.user_model');
        $userModel::where('current_team_id', $id)
            ->update(['current_team_id' => null]);

        return redirect(route('teams.index'));
    }
}
