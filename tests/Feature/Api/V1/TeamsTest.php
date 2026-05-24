<?php

namespace Tests\Feature\Api\V1;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/teams — list the caller's teams and switch the active tenant.
 */
class TeamsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_the_callers_own_teams()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.teams.index'));

        $response->assertOk();
        $this->assertCount(1, $response->json('teams'));
        $this->assertTrue($response->json('teams.0.is_owner'));
        $this->assertTrue($response->json('teams.0.is_current'));
    }

    #[Test]
    public function switch_changes_the_users_current_team_when_they_are_a_member()
    {
        $user = $this->createUser();
        $secondary = create(Team::class, ['owner_id' => $user->id]);
        $user->attachTeam($secondary->id);

        Sanctum::actingAs($user);

        $this->postJson(route('api.v1.teams.switch', $secondary->uuid))
            ->assertOk();

        $this->assertSame($secondary->id, $user->fresh()->current_team_id);
    }

    #[Test]
    public function switching_to_a_team_the_caller_doesnt_belong_to_returns_403()
    {
        $alice = $this->createUser();
        $alicesTeam = create(Team::class, ['owner_id' => $alice->id]);
        $alice->attachTeam($alicesTeam->id);

        $bob = $this->createUser();
        Sanctum::actingAs($bob);

        $this->postJson(route('api.v1.teams.switch', $alicesTeam->uuid))
            ->assertStatus(403);
    }

    #[Test]
    public function teams_endpoints_require_authentication()
    {
        $this->getJson(route('api.v1.teams.index'))->assertStatus(401);
    }
}
