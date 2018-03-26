<?php

namespace App\Models;

use Mpociot\Teamwork\TeamworkTeam;

class Team extends TeamworkTeam
{
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
