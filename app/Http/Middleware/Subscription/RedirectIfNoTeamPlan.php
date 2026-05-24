<?php

namespace App\Http\Middleware\Subscription;

use Closure;

class RedirectIfNoTeamPlan
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
        if (auth()->user()->doesNotHaveTeamSubscription()) {
            flashInfo('Your current plan does not include team management.');

            return redirect()->route('user_settings.profile.show');
        }

        return $next($request);
    }
}
