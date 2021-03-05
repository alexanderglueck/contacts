<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\TwoFactor\TwoFactorSuccess::class => [
            \App\Listeners\Log\LogTwoFactorSuccess::class,
        ],
        \App\Events\TwoFactor\TwoFactorFailure::class => [
            \App\Listeners\Log\LogTwoFactorFailure::class,
        ],
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\Log\LogSuccessfulLogin::class,
        ],

        \Illuminate\Auth\Events\Failed::class => [
            \App\Listeners\Log\LogFailedLogin::class,
        ],

        \App\Events\Tenant\TenantIdentified::class => [
            \App\Listeners\Tenant\RegisterTenant::class,
            \App\Listeners\Tenant\UseTenantFilesystem::class,
        ],

        \App\Events\Tenant\TenantWasCreated::class => [
            \App\Listeners\Tenant\CreateTenantDatabaseEntry::class,
        ],

        \App\Events\Auth\UserSignedUp::class => [
            \App\Listeners\Auth\SendActivationEmail::class,
        ],

        \App\Events\Auth\UserRequestedActivationEmail::class => [
            \App\Listeners\Auth\SendActivationEmail::class,
        ],

        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],

        \App\Events\Auth\UserChangedPassword::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],

        \App\Events\WebsocketTest::class => [
            \App\Listeners\SendEventToBrowser::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
