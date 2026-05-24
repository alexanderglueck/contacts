<?php

namespace App\Http\Middleware\Tenant;

use App\Events\Tenant\TenantIdentified;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;

class SetTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Resolution order:
        //   1) session('tenant') — set by PrimeTenantSession on web login
        //   2) ?tenant=UUID query  — used by URL-only API clients (iCal feed)
        //   3) user's currentTeam  — fallback for stateless API requests so
        //      mobile clients don't have to send ?tenant on every call
        $tenant = $this->resolveTenant(session('tenant'))
            ?? ($request->has('tenant') ? $this->resolveTenant($request->get('tenant')) : null)
            ?? $user?->currentTeam;

        if (! $tenant) {
            return $this->reject($request, 'No tenant context available.', 400);
        }

        if (! $user || ! $user->teams->contains('id', $tenant->id)) {
            return $this->reject($request, 'You are not a member of that team.', 403);
        }

        event(new TenantIdentified($tenant));

        return $next($request);
    }

    protected function resolveTenant($uuid): ?Team
    {
        return $uuid ? Team::where('uuid', $uuid)->first() : null;
    }

    private function reject(Request $request, string $message, int $apiStatus)
    {
        // API / fetch clients can't follow our /select redirect — return
        // JSON so Android can surface a sensible error to the user.
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $apiStatus);
        }

        return redirect()->route('tenant.index');
    }
}
