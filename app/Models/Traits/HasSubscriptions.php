<?php

namespace App\Models\Traits;

trait HasSubscriptions
{
    public function hasPiggybackSubscription(): bool
    {
        if ($this->currentTeam == null) {
            return false;
        }

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

    public function hasCancelled()
    {
        return optional($this->subscription('main'))->cancelled();
    }

    public function hasNotCancelled()
    {
        return ! $this->hasCancelled();
    }

    public function isCustomer()
    {
        return $this->hasStripeId();
    }

    public function hasTeams()
    {
        return $this->teams()->count() > 0;
    }

    public function hasTeamSubscription()
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
