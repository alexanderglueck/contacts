<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Mpociot\Teamwork\TeamInvite as BaseTeamInvite;

class TeamInvite extends BaseTeamInvite
{
    use HasUlidRouteKey;
}
