<?php

namespace App\Http\Middleware;

use App\Tenant\Manager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the current tenant has `two_factor_required` set, every tenant-scoped
 * route is gated on the user having Fortify-confirmed two-factor enabled.
 * Failing members are redirected to the 2FA setup page with a flash notice.
 *
 * Scoped only to the tenant route group (registered in bootstrap/app.php's
 * withRouting `then:` callback). That means /settings/two-factor itself,
 * Fortify's password-confirm flow, and /logout remain reachable so the user
 * can actually satisfy the requirement.
 */
class EnforceTeamTwoFactor
{
    public function __construct(private Manager $tenants)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenants->getTenant();

        if (! $tenant || ! $tenant->two_factor_required) {
            return $next($request);
        }

        $user = $request->user();
        if ($user && $user->two_factor_confirmed_at !== null) {
            return $next($request);
        }

        Session::flash(
            'alert-warning',
            'This team requires two-factor authentication. Please enable it to continue.',
        );

        return redirect()->route('user_settings.two_factor.edit');
    }
}
