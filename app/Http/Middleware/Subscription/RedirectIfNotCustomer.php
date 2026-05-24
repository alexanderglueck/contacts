<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfNotCustomer
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
        if ( ! auth()->user()->isCustomer()) {
            flashInfo('You need to start a subscription before you can manage billing details.');

            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
