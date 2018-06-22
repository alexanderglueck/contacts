<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\TwoFactor\TwoFactorSuccess' => [
            'App\Listeners\Log\LogTwoFactorSuccess',
        ],
        'App\Events\TwoFactor\TwoFactorFailure' => [
            'App\Listeners\Log\LogTwoFactorFailure',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\Log\LogSuccessfulLogin',
        ],

        'Illuminate\Auth\Events\Failed' => [
            'App\Listeners\Log\LogFailedLogin',
        ],

        'App\Events\Tenant\TenantIdentified' => [
            'App\Listeners\Tenant\RegisterTenant',
            'App\Listeners\Tenant\UseTenantFilesystem',
        ],

        'App\Events\Tenant\TenantWasCreated' => [
            'App\Listeners\Tenant\CreateTenantDatabase',
        ],

        'App\Events\Auth\UserSignedUp' => [
            'App\Listeners\Auth\SendActivationEmail',
        ],

        'App\Events\Auth\UserRequestedActivationEmail' => [
            'App\Listeners\Auth\SendActivationEmail',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
