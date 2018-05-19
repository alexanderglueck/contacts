<?php

namespace Tests;

use App\Models\Team;
use App\Models\User;
use App\Events\Tenant\TenantIdentified;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createUser($permission = [])
    {
        /** @var User $user */
        $user = create(User::class, [
            'current_team_id' => null
        ]);

        $team = create(Team::class, [
            'owner_id' => $user->id
        ]);

        $user->teams()->attach($team->id);
        $user->update([
            'current_team_id' => $team->id
        ]);

        session()->put('tenant', $team->uuid);

       // DB::disconnect('tenant');
       // DB::purge('tenant');
       // DB::disconnect('testing');
       // DB::purge('testing');
      //  DB::reconnect('testing');
      //  DB::setDefaultConnection('testing');

        event(new TenantWasCreated($team, $user));

        event(new TenantIdentified($team));

        $this->be($user);

        $this->session([
            'tenant' => $team->uuid
        ]);

        $user->givePermissionTo($permission);

        return $user;
    }
}
