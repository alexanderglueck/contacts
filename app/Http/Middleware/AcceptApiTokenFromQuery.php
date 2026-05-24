<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Promote a ?api_token= query parameter to an Authorization: Bearer header
 * before authentication middleware (e.g. auth:api / Sanctum) runs.
 *
 * Calendar apps (Google Calendar, Apple Calendar, Outlook) and similar
 * URL-only subscribers can't send custom headers when subscribing to a
 * feed — they just GET the URL. This adapter lets the same Sanctum tokens
 * work for those clients without weakening the default header-only path.
 */
class AcceptApiTokenFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken() && $request->query('api_token')) {
            $request->headers->set('Authorization', 'Bearer '.$request->query('api_token'));
        }

        return $next($request);
    }
}
