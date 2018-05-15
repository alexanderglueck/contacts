<?php

namespace App\Http\Middleware;

use Closure;

class ChecksExpiredConfirmationTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $redirect)
    {
        if ($request->token->hasExpired()) {
            flashError('Token expired. ');

            return redirect($redirect);
        }

        return $next($request);
    }
}
