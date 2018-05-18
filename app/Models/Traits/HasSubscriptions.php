<?php

namespace App\Models\Traits;

trait HasSubscriptions
{
    public function hasPiggybackSubscription(): bool
    {
        if ($this->id == $this->currentTeam->owner->id) {
            return false;
        }

        if (auth()->user()->currentTeam->owner->hasSubscription()) {
            return true;
        }

        return false;
    }

    public function hasSubscription($subscription = 'main'): bool
    {
        if ($this->hasPiggybackSubscription()) {
            return true;
        }

        return $this->subscribed($subscription);
    }

    public function hasNoSubscription($subscription = 'main'): bool
    {
        return ! $this->hasSubscription($subscription);
    }

    public function hasCancelled(): bool
    {
        return optional($this->subscription('main'))->cancelled();
    }

    public function hasNotCancelled(): bool
    {
        return ! $this->hasCancelled();
    }

    public function isCustomer(): bool
    {
        return $this->hasStripeId();
    }

    public function hasTeamSubscription(): bool
    {
        foreach ($this->plans as $plan) {
            if ($plan->isForTeams()) {
                return true;
            }
        }

        return false;
    }

    public function doesNotHaveTeamSubscription(): bool
    {
        return ! $this->hasTeamSubscription();
    }
}
