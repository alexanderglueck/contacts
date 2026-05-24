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

            // Grant a 30-day complimentary trial so new sign-ups can try every
            // feature without going through Stripe. trial_ends_at + status=active
            // is what Cashier's onTrial()/valid() rely on. ends_at MUST stay
            // null — any value there makes canceled() return true, putting the
            // user into a "trial that's been pre-canceled" state that confuses
            // every subscription-state middleware downstream.
            $user->subscriptions()->create([
                'type' => 'main',
                'stripe_id' => 'trial_'.Str::random(40),
                'stripe_status' => 'active',
                'stripe_price' => 'team_month_10',
                'quantity' => 1,
                'trial_ends_at' => now()->addDays(30),
            ]);

            event(new TenantWasCreated($team, $user));

            return $user;
        });
    }
}
