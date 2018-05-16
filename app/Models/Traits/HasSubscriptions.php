<?php

namespace App\Models\Traits;

trait HasSubscriptions
{
    public function hasSubscription($subscription = 'main')
    {
        return $this->subscribed($subscription);
    }

    public function hasNoSubscription($subscription = 'main')
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
}
