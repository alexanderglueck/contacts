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
            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
