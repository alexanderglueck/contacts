<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfNotActive
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
        if ( ! auth()->check() || auth()->user()->hasNoSubscription()) {
            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
