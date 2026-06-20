<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Model;

/**
 * Pending invitation of an email address to a team. Formerly extended
 * mpociot/teamwork's base TeamInvite; now a plain first-party model on the
 * same `team_invites` table, with the token-based accept/deny flow preserved.
 */
class TeamInvite extends Model
{
    use HasUlidRouteKey;

    protected $table = 'team_invites';

    protected $guarded = [];

    /**
     * The team the invitation is for.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * The (possibly not-yet-registered) user matched by the invited email.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    /**
     * The user who sent the invitation.
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
