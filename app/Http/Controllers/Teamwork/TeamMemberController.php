<?php

namespace App\Http\Controllers\Teamwork;

use App\Mail\TeamInvitation;
use Illuminate\Http\Request;
use Mpociot\Teamwork\TeamInvite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;

class TeamMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the members of the given team.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($id);

        return view('teamwork.members.list')->withTeam($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $team_id
     * @param int $user_id
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($team_id, $user_id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);
        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        $userModel = config('teamwork.user_model');
        $user = $userModel::findOrFail($user_id);
        if ($user->getKey() === auth()->user()->getKey()) {
            abort(403);
        }

        // Decrease the quantity because a user is being removed
        $team->owner->subscription('main')->decrementQuantity();

        $user->detachTeam($team);

        $user->current_team_id = $user->teams()->first()->id;
        $user->save();

        return redirect(route('teams.index'));
    }

    /**
     * @param Request $request
     * @param int     $team_id
     *
     * @return $this
     */
    public function invite(Request $request, $team_id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $teamModel = config('teamwork.team_model');
        $team = $teamModel::findOrFail($team_id);

        if ( ! Teamwork::hasPendingInvite($request->email, $team)) {
            Teamwork::inviteToTeam($request->email, $team, function ($invite) {
                Mail::to($invite->email)->send(new TeamInvitation($invite));
                // Send email to user
            });
        } else {
            return redirect()->back()->withErrors([
                'email' => 'The email address is already invited to the team.'
            ]);
        }

        return redirect(route('teams.members.show', $team->id));
    }

    /**
     * Resend an invitation mail.
     *
     * @param $invite_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resendInvite($invite_id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $invite = TeamInvite::findOrFail($invite_id);
        Mail::to($invite->email)->send(new TeamInvitation($invite));

        return redirect(route('teams.members.show', $invite->team));
    }
}
