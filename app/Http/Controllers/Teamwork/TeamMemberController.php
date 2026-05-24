<?php

namespace App\Http\Controllers\Teamwork;

use App\Mail\TeamInvitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\TeamInvite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
     */
    public function show(Team $team): Response|RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $authUser = Auth::user();
        $isOwner = $authUser->isOwnerOfTeam($team);
        $isCurrentTeam = $authUser->currentTeam && $authUser->currentTeam->id === $team->id;
        $canImpersonate = $authUser->checkPermissionTo('impersonate users');

        return Inertia::render('Teamwork/Members/Show', [
            'team' => [
                'uuid' => $team->uuid,
                'name' => $team->name,
                'users' => $team->users->map(fn ($user) => [
                    'slug' => $user->slug,
                    'name' => $user->name,
                    'is_self' => $user->id === $authUser->id,
                ])->all(),
                'invites' => $team->invites->map(fn ($invite) => [
                    'ulid' => $invite->ulid,
                    'email' => $invite->email,
                ])->all(),
            ],
            'can' => [
                'manage' => $isOwner,
                'impersonate' => $canImpersonate && $isCurrentTeam,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team, User $user): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

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

    public function invite(Request $request, Team $team): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

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

        return redirect(route('teams.members.show', $team->uuid));
    }

    /**
     * Resend an invitation mail.
     */
    public function resendInvite(TeamInvite $invite): RedirectResponse
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        Mail::to($invite->email)->send(new TeamInvitation($invite));

        return redirect(route('teams.members.show', $invite->team->uuid));
    }
}
