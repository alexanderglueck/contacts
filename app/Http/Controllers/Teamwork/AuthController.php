<?php

namespace App\Http\Controllers\Teamwork;

use App\Domain\Teams\TeamInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * Accept the given invite
     */
    public function acceptInvite(string $token, TeamInvitationService $invitations): RedirectResponse
    {
        $invite = $invitations->fromAcceptToken($token);
        if ( ! $invite) {
            abort(404);
        }

        if (auth()->check()) {
            $team = $invite->team;
            $owner = $team->owner;

            // increase the quantity because a new user joined
            $owner->subscription('main')->incrementQuantity();

            $invitations->accept($invite, auth()->user());

            return redirect()->route('teams.index');
        } else {
            session(['invite_token' => $token]);

            return redirect()->to('login');
        }
    }
}
