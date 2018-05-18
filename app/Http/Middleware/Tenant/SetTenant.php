<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use App\Models\Team;
use App\Events\Tenant\TenantIdentified;

class SetTenant
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
        $tenant = $this->resolveTenant(session('tenant'));

        if ( ! $tenant) {
            return $next($request);
        }

        if ( ! auth()->user()->teams->contains('id', $tenant->id)) {

            if (auth()->user()->currentTeam->id != $tenant->id) {
                session()->put('tenant', auth()->user()->currentTeam->uuid);

                return $this->allowThrough($next, $request, auth()->user()->currentTeam);
            }

            return redirect()->route('home');
        }

        return $this->allowThrough($next, $request, $tenant);
    }

    protected function allowThrough($next, $request, $tenant)
    {
        event(new TenantIdentified($tenant));

        return $next($request);
    }

    protected function resolveTenant($uuid)
    {
        return Team::where('uuid', $uuid)->first();
    }
}
