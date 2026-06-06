<?php

use App\Console\Commands\Notification\SendDailyEmail;
use App\Console\Commands\Notification\SendWeeklyEmail;
use App\Http\Middleware\AuthenticateRegister;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\Impersonate;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\Subscription\RedirectIfCancelled;
use App\Http\Middleware\Subscription\RedirectIfNoTeamPlan;
use App\Http\Middleware\Subscription\RedirectIfNotActive;
use App\Http\Middleware\Subscription\RedirectIfNotCancelled;
use App\Http\Middleware\Subscription\RedirectIfNotCustomer;
use App\Http\Middleware\Subscription\RedirectIfNotInactive;
use App\Http\Middleware\Subscription\RedirectIfPiggybackSubscription;
use App\Http\Middleware\Tenant\SetTenant;
use App\Http\Middleware\VerifyCorrectContact;
use App\Services\UpcomingEvents;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        then: function () {
            Route::middleware(['web', 'auth', 'tenant', 'enforce_team_2fa'])
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withBroadcasting(__DIR__.'/../routes/channels.php')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->appendToGroup('web', [
            Impersonate::class,
            \App\Http\Middleware\SetLocale::class,
            HandleInertiaRequests::class,
        ]);

        $middleware->group('tenant', [
            SetTenant::class,
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'admin' => IsAdmin::class,
            'verify_contact' => VerifyCorrectContact::class,
            'auth.register' => AuthenticateRegister::class,
            'api_token.query' => \App\Http\Middleware\AcceptApiTokenFromQuery::class,
            'enforce_team_2fa' => \App\Http\Middleware\EnforceTeamTwoFactor::class,
            'subscription.active' => RedirectIfNotActive::class,
            'subscription.notcancelled' => RedirectIfCancelled::class,
            'subscription.cancelled' => RedirectIfNotCancelled::class,
            'subscription.customer' => RedirectIfNotCustomer::class,
            'subscription.inactive' => RedirectIfNotInactive::class,
            'subscription.team' => RedirectIfNoTeamPlan::class,
            'subscription.owner' => RedirectIfPiggybackSubscription::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command(SendDailyEmail::class)
            ->dailyAt('06:00')
            ->timezone('Europe/Vienna')
            ->when(function () {
                // Fire when there is anything to report today — birthdays
                // (date_of_birth) as well as important dates (ContactDate).
                return UpcomingEvents::eventsOnDate(new DateTime())->isNotEmpty();
            });

        $schedule->command(SendWeeklyEmail::class)
            ->mondays()
            ->at('06:00')
            ->timezone('Europe/Vienna')
            ->when(function () {
                // Cover this week and next week, including birthdays.
                $start = DateTime::createFromFormat('Ymd', date('Ymd'));
                $end = DateTime::createFromFormat('Ymd', date('Ymd'))
                    ->add(DateInterval::createFromDateString('2 weeks'))
                    ->sub(DateInterval::createFromDateString('1 day'));

                return UpcomingEvents::eventsInRange($start, $end)->isNotEmpty();
            });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
