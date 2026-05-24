<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfPiggybackSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->hasPiggybackSubscription()) {
            flashInfo('Subscription management is handled by your team owner.');

            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
