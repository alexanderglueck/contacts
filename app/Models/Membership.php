<?php

namespace App\Models;

use Laravel\Jetstream\Membership as JetstreamMembership;

/**
 * Concrete pivot model backing the team_user table for Jetstream's HasTeams
 * trait. Jetstream resolves this class via Jetstream::membershipModel(), which
 * defaults to App\Models\Membership.
 */
class Membership extends JetstreamMembership
{
    //
}
