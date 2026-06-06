<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * team:grant-permanent-plan — grants a team's owner the biggest active
 * multi-team plan, permanently (active, no trial, no end date).
 */
class GrantPermanentPlanTest extends TestCase
{
    use RefreshDatabase;

    private function biggestTeamPlan(): Plan
    {
        return Plan::active()->forTeams()
            ->orderByRaw('teams_limit IS NULL DESC')
            ->orderByDesc('teams_limit')
            ->first();
    }

    #[Test]
    public function it_grants_a_main_subscription_to_an_owner_without_one()
    {
        $owner = create(User::class);
        $team = create(Team::class, ['owner_id' => $owner->id]);
        $plan = $this->biggestTeamPlan();

        $this->artisan('team:grant-permanent-plan', ['team' => $team->id])
            ->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $owner->id,
            'type' => 'main',
            'stripe_status' => 'active',
            'stripe_price' => $plan->gateway_id,
            'ends_at' => null,
            'trial_ends_at' => null,
        ]);

        $this->assertTrue($owner->fresh()->hasTeamSubscription());
    }

    #[Test]
    public function it_upgrades_an_existing_main_subscription_in_place()
    {
        $owner = create(User::class);
        $team = create(Team::class, ['owner_id' => $owner->id]);
        $plan = $this->biggestTeamPlan();

        // An existing personal-plan subscription that is about to expire.
        $existing = $owner->subscriptions()->create([
            'type' => 'main',
            'stripe_id' => 'old_'.\Illuminate\Support\Str::random(20),
            'stripe_status' => 'active',
            'stripe_price' => 'monthly',
            'quantity' => 1,
            'ends_at' => now()->addDay(),
        ]);

        $this->artisan('team:grant-permanent-plan', ['team' => $team->id])
            ->assertSuccessful();

        // Same row, upgraded and made permanent — not a duplicate.
        $this->assertSame(1, $owner->subscriptions()->count());
        $this->assertDatabaseHas('subscriptions', [
            'id' => $existing->id,
            'stripe_price' => $plan->gateway_id,
            'ends_at' => null,
        ]);
    }

    #[Test]
    public function it_accepts_the_team_uuid_too()
    {
        $owner = create(User::class);
        $team = create(Team::class, ['owner_id' => $owner->id]);

        $this->artisan('team:grant-permanent-plan', ['team' => $team->uuid])
            ->assertSuccessful();

        $this->assertTrue($owner->fresh()->hasTeamSubscription());
    }

    #[Test]
    public function it_fails_for_an_unknown_team()
    {
        $this->artisan('team:grant-permanent-plan', ['team' => '999999'])
            ->assertFailed();
    }
}
