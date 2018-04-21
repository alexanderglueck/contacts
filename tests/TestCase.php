<?php

namespace Tests;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createUser($permission = [])
    {
        /** @var User $user */
        $user = create(User::class);

        $team = create(Team::class, [
            'owner_id' => $user->id
        ]);

        $user->attachTeam($team);

        $this->be($user);

        $user->givePermissionTo($permission);

        return $user;
    }
}
