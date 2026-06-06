<?php

namespace App\Providers;

use App\Imports\MappingHolder;
use App\Models\ContactAddress;
use App\Observers\ContactAddressObserver;
use App\Support\Scramble\FileMaxKilobytesTransformer;
use Dedoc\Scramble\Scramble;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MappingHolder::class, function () {
            return new MappingHolder();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Fix for the "Specified key was too long" error
         *
         * @see https://laravel-news.com/laravel-5-4-key-too-long-error
         */
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        // Throttle Nominatim geocoding to its public usage policy (1 req/sec).
        // The GeocodeContactAddress job opts into this limiter via its
        // middleware(); over-quota jobs are released back to the queue.
        RateLimiter::for('nominatim', fn () => Limit::perSecond(1));

        // Make file-upload size limits in OpenAPI accurate: Scramble's
        // default `max` transformer treats Laravel's KB value as a raw
        // OpenAPI maxLength (interpreted as bytes by tooling), which
        // under-represents an 8 MB cap as 8192 bytes. The transformer
        // converts the units and prepends a human-readable size to the
        // schema description.
        Scramble::configure()->withRuleTransformers([FileMaxKilobytesTransformer::class]);

        ContactAddress::observe(ContactAddressObserver::class);

        // Let Sanctum read the token from a ?api_token= query param as a
        // fallback to the Authorization header. Calendar clients (Google,
        // Apple, Outlook) subscribing to the iCal feed can only GET a URL,
        // they can't send headers.
        //
        // This is done at the Sanctum layer rather than via a middleware that
        // rewrites the header, because Laravel's middleware-priority sorting
        // hoists the `auth` middleware ahead of any custom route middleware on
        // routes that use the `web` group — so a header-rewriting middleware
        // would run *after* authentication and be ignored. Reading the token
        // here is order-independent.
        Sanctum::getAccessTokenFromRequestUsing(function (Request $request) {
            if ($bearer = $request->bearerToken()) {
                return $bearer;
            }

            // Only honour the token in the URL for the iCal feed — tokens in
            // query strings leak into access logs, so we don't accept them on
            // the rest of the API.
            return $request->routeIs('ical') ? $request->query('api_token') : null;
        });
    }
}
