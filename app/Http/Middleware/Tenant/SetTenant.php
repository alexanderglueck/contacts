<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Events\Tenant\TenantIdentified;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         * Try to find a tenant that matches the UUID stored in the session
         */
        $tenant = $this->resolveTenant(session('tenant'));

        if (! $tenant) {
            /*
             * If no matching tenant could be found redirect to the select tenant
             * page
             */
            return redirect()->route('tenant.index');
        }

        if (! auth()->user()->teams->contains('id', $tenant->id)) {
            /*
             * The users team was deleted or the user was kicked out
             * of the team. Redirect to the select tenant page
             */
            return redirect()->route('tenant.index');
        }

        if (! auth()->user()->currentTeam) {
            /*
             * The user does not have a team assigned. Redirect to select tenant
             * page to prevent issues on other pages
             */
            return redirect()->route('tenant.index');
        }

        /*
         * The tenant exists, the user has access to the tenant and has a
         * tenant set as current tenant
         */
        event(new TenantIdentified($tenant));

        return $next($request);
    }

    protected function resolveTenant($uuid)
    {
        return Team::where('uuid', $uuid)->first();
    }
}
