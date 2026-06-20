<?php

namespace App\Domain\Teams;

use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;

/**
 * First-party replacement for the mpociot/teamwork invite facade. Keeps the
 * existing token-based flow: an invite carries an accept_token (and deny_token)
 * and is consumed by attaching the accepting user to the team.
 */
class TeamInvitationService
{
    public function hasPendingInvite(string $email, Team $team): bool
    {
        return TeamInvite::query()
            ->where('email', $email)
            ->where('team_id', $team->id)
            ->exists();
    }

    public function invite(string $email, Team $team, User $inviter): TeamInvite
    {
        return TeamInvite::create([
            'user_id' => $inviter->id,
            'team_id' => $team->id,
            'type' => 'invite',
            'email' => $email,
            'accept_token' => md5(uniqid(microtime(), true)),
            'deny_token' => md5(uniqid(microtime(), true)),
        ]);
    }

    public function fromAcceptToken(string $token): ?TeamInvite
    {
        return TeamInvite::query()->where('accept_token', $token)->first();
    }

    /**
     * Accept an invite on behalf of the given user: join the team, drop the
     * invitation.
     */
    public function accept(TeamInvite $invite, User $user): void
    {
        $user->attachTeam($invite->team);

        $invite->delete();
    }
}
