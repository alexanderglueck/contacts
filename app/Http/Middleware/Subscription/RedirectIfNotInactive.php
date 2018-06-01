<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfNotInactive
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
        if (auth()->user()->hasSubscription()) {
            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
