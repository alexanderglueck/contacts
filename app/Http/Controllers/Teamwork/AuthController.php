<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Mpociot\Teamwork\Facades\Teamwork;

class AuthController extends Controller
{
    /**
     * Accept the given invite
     */
    public function acceptInvite(string $token): RedirectResponse
    {
        $invite = Teamwork::getInviteFromAcceptToken($token);
        if ( ! $invite) {
            abort(404);
        }

        if (auth()->check()) {
            $team = $invite->team;
            $owner = $team->owner;

            // increase the quantity because a new user joined
            $owner->subscription('main')->incrementQuantity();

            Teamwork::acceptInvite($invite);

            return redirect()->route('teams.index');
        } else {
            session(['invite_token' => $token]);

            return redirect()->to('login');
        }
    }
}
