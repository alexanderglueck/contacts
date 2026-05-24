<?php

namespace App\Actions\Fortify;

use App\Events\Tenant\TenantWasCreated;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'terms' => ['accepted'],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $team = Team::create([
                'name' => $user->name,
                'owner_id' => $user->id,
            ]);

            $user->attachTeam($team);

            // Grant a 30-day complimentary subscription to the team-enabled plan
            // so new sign-ups can try every feature without going through Stripe.
            // ends_at in the future + stripe_status=active satisfies Cashier's
            // `active()` check (onGracePeriod = true) for the trial window.
            $user->subscriptions()->create([
                'type' => 'main',
                'stripe_id' => 'trial_'.Str::random(40),
                'stripe_status' => 'active',
                'stripe_price' => 'team_month_10',
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(30),
                'ends_at' => now()->addDays(30),
            ]);

            event(new TenantWasCreated($team, $user));

            return $user;
        });
    }
}
