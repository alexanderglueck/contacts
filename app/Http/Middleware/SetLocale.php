<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Snap the framework's locale to the signed-in user's preference for
     * the duration of this request. Falls back to the app default, which
     * also covers guests, missing-column situations, and anyone who hasn't
     * picked a language yet. The allowlist is intentionally narrow — an
     * attacker controlling the user_id row can't pivot it into setting
     * the locale to something unsupported.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && in_array($user->locale, config('app.available_locales', ['en']), true)) {
            App::setLocale($user->locale);
        }

        return $next($request);
    }
}
