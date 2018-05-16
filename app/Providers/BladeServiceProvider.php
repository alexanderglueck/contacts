<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('impersonating', function () {
            return auth()->check() && session()->has('impersonate');
        });

        Blade::if('subscribed', function () {
            return auth()->check() && auth()->user()->hasSubscription();
        });

        Blade::if('notsubscribed', function () {
            return ( ! auth()->check()) || auth()->user()->hasNoSubscription();
        });

        Blade::if('subscriptioncancelled', function () {
            return auth()->check() && auth()->user()->hasCancelled();
        });

        Blade::if('subscriptionnotcancelled', function () {
            return auth()->user()->hasNotCancelled();
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
