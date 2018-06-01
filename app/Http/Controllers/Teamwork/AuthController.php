<?php

namespace App\Http\Controllers\Teamwork;

use Illuminate\Routing\Controller;
use Mpociot\Teamwork\Facades\Teamwork;

class AuthController extends Controller
{
    /**
     * Accept the given invite
     *
     * @param $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvite($token)
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
