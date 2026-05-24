<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Support\UserAgentInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SessionsController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('UserSettings/Sessions', [
            'sessions' => $this->sessionsFor($request),
            'driver' => Config::get('session.driver'),
        ]);
    }

    /**
     * Kill a single session row. Restricted to the caller's own user_id
     * so a victim of a leaked browser cookie can ID-swap and revoke
     * just that device without touching everything else.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();

        if ($id === $request->session()->getId()) {
            return back()->with('alert-danger', 'Use the "log out other browsers" action for sessions other than this one.');
        }

        DB::connection(Config::get('session.connection'))
            ->table(Config::get('session.table', 'sessions'))
            ->where('id', $id)
            ->where('user_id', $user->getAuthIdentifier())
            ->delete();

        return back()->with('alert-success', 'Session signed out.');
    }

    /**
     * Equivalent of Jetstream's "Log Out Other Browser Sessions" — rotates
     * the password rehash that Laravel stamps in the session and deletes
     * all other session rows for this user. Gated by re-entering the
     * password so a hijacker can't lock the real owner out.
     */
    public function destroyOthers(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logoutOtherDevices($request->input('password'));

        DB::connection(Config::get('session.connection'))
            ->table(Config::get('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return back()->with('alert-success', 'All other browsers have been signed out.');
    }

    /**
     * Return the active session rows for this user, decorated with a
     * human-readable label and a flag marking the current browser.
     *
     * Returns an empty list when the app isn't using the database
     * driver — there's no source table to query. The UI shows a
     * "set SESSION_DRIVER=database to enable" notice in that case.
     */
    private function sessionsFor(Request $request): array
    {
        if (Config::get('session.driver') !== 'database') {
            return [];
        }

        $currentId = $request->session()->getId();
        $userId = $request->user()->getAuthIdentifier();

        return DB::connection(Config::get('session.connection'))
            ->table(Config::get('session.table', 'sessions'))
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentId) {
                $agent = UserAgentInfo::fromUserAgent($session->user_agent);

                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'browser' => $agent['browser'],
                    'platform' => $agent['platform'],
                    'is_desktop' => $agent['is_desktop'],
                    'is_current' => $session->id === $currentId,
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'last_active_iso' => Carbon::createFromTimestamp($session->last_activity)->toIso8601String(),
                ];
            })
            ->all();
    }
}
