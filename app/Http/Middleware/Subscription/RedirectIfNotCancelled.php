<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfNotCancelled
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
        if (auth()->user()->hasNotCancelled()) {
            flashInfo('That page is only relevant while your subscription is cancelled.');

            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
