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
            flashInfo('That page is unavailable while your subscription is cancelled.');

            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
