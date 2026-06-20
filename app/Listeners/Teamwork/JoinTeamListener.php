<?php

namespace App\Listeners\Teamwork;

use App\Domain\Teams\TeamInvitationService;

class JoinTeamListener
{
    public function __construct(private TeamInvitationService $invitations)
    {
    }

    /**
     * See if the session contains an invite token on login and try to accept
     * it.
     *
     * @param mixed $event
     */
    public function handle($event)
    {
        if (session('invite_token')) {
            if ($invite = $this->invitations->fromAcceptToken(session('invite_token'))) {
                $this->invitations->accept($invite, $event->user);
            }
            session()->forget('invite_token');
        }
    }
}
