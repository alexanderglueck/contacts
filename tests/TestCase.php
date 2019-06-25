<?php

namespace Tests;

use App\Models\Team;
use App\Models\User;
use Laravel\Cashier\Subscription;
use App\Events\Tenant\TenantIdentified;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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

        create(Subscription::class, [
            'user_id' => $user->id
        ]);

        $user->attachTeam($team->id);

        session()->put('tenant', $team->uuid);


        $this->be($user);

        event(new TenantWasCreated($team, $user));

        event(new TenantIdentified($team));

        $this->session([
            'tenant' => $team->uuid
        ]);

        $user->givePermissionTo($permission);

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan("db:seed");
    }


}
