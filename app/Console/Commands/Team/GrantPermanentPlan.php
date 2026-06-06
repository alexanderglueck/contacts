<?php

namespace App\Console\Commands\Team;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Grants a team the biggest active multi-team plan, permanently and without
 * going through Stripe.
 *
 * A team's plan is determined by its owner's "main" Cashier subscription (team
 * members piggyback on it), so this upgrades the owner's main subscription —
 * creating one if absent. "Permanent" means active with no trial and no end
 * date (ends_at MUST stay null, otherwise Cashier treats it as canceled).
 */
class GrantPermanentPlan extends Command
{
    protected $signature = 'team:grant-permanent-plan {team : The team id or uuid}';

    protected $description = 'Upgrade a team to the permanent biggest multi-team plan (no Stripe).';

    public function handle(): int
    {
        $identifier = (string) $this->argument('team');

        $team = Team::where('id', $identifier)->orWhere('uuid', $identifier)->first();

        if (! $team) {
            $this->error("No team found for id/uuid [{$identifier}].");

            return self::FAILURE;
        }

        $owner = $team->owner;

        if (! $owner) {
            $this->error("Team #{$team->id} has no owner to attach a subscription to.");

            return self::FAILURE;
        }

        // Biggest = largest teams_limit; a null limit means unlimited, so it
        // sorts ahead of any finite limit.
        $plan = Plan::active()->forTeams()
            ->orderByRaw('teams_limit IS NULL DESC')
            ->orderByDesc('teams_limit')
            ->first();

        if (! $plan) {
            $this->error('No active multi-team plan exists to grant.');

            return self::FAILURE;
        }

        /** @var Subscription|null $subscription */
        $subscription = $owner->subscription('main');

        if ($subscription) {
            $subscription->forceFill([
                'stripe_status' => 'active',
                'stripe_price' => $plan->gateway_id,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ])->save();
            $action = 'Upgraded';
        } else {
            $owner->subscriptions()->create([
                'type' => 'main',
                'stripe_id' => 'manual_'.Str::random(40),
                'stripe_status' => 'active',
                'stripe_price' => $plan->gateway_id,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ]);
            $action = 'Granted';
        }

        $limit = $plan->teams_limit ?? 'unlimited';
        $this->info(
            "{$action} team #{$team->id} ({$team->name}) the permanent plan ".
            "\"{$plan->name}\" (limit: {$limit}) via owner {$owner->email}."
        );

        return self::SUCCESS;
    }
}
