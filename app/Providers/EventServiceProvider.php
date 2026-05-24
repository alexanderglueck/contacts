<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\Auth\PrimeTenantSession::class,
            \App\Listeners\Log\LogSuccessfulLogin::class,
        ],

        \Illuminate\Auth\Events\Failed::class => [
            \App\Listeners\Log\LogFailedLogin::class,
        ],

        \Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided::class => [
            \App\Listeners\Log\LogTwoFactorSuccess::class,
        ],

        \Laravel\Fortify\Events\TwoFactorAuthenticationFailed::class => [
            \App\Listeners\Log\LogTwoFactorFailure::class,
        ],

        \App\Events\Tenant\TenantIdentified::class => [
            \App\Listeners\Tenant\RegisterTenant::class,
            \App\Listeners\Tenant\UseTenantFilesystem::class,
        ],

        \App\Events\Tenant\TenantWasCreated::class => [
            \App\Listeners\Tenant\CreateTenantDatabaseEntry::class,
        ],

        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],

        \App\Events\Auth\UserChangedPassword::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],

        \App\Events\WebsocketTest::class => [
            \App\Listeners\SendEventToBrowser::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
