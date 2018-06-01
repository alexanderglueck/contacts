<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfCancelled
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
        if ( ! auth()->user()->hasSubscription() || auth()->user()->hasCancelled()) {
            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
