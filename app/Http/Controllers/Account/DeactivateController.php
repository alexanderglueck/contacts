<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\DeactivateStoreRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DeactivateController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('UserSettings/Deactivate', [
            'subscriptionNotCancelled' => $user ? $user->hasNotCancelled() : false,
            'lastMemberTeams' => $user ? $this->teamsLeftOrphanedBy($user) : [],
        ]);
    }

    public function store(DeactivateStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Defense in depth: re-check on POST in case the user pushed past
        // the disabled button via dev tools or a stale page state.
        $orphaned = $this->teamsLeftOrphanedBy($user);
        if (! empty($orphaned)) {
            flashError('You are the last active member of '
                .collect($orphaned)->pluck('name')->join(', ')
                .'. Use "Delete account" instead — deactivation would leave the team without any active user.');

            return redirect()->route('user_settings.deactivate.index');
        }

        if ($user->subscribed('main')) {
            $user->subscription('main')->cancel();
        }

        auth()->logout();
        $user->delete();

        flashSuccess('Your account has been deactivated');

        return redirect()->route('login');
    }

    /**
     * Return the teams that would have zero active members if this user
     * were deactivated. Used both to drive the Vue notice and to gate
     * the POST handler.
     *
     * @return array<int, array{name: string}>
     */
    private function teamsLeftOrphanedBy(User $user): array
    {
        return $user->teams
            ->filter(fn (Team $team) => $team->users()
                ->where('users.id', '!=', $user->id)
                ->count() === 0)
            ->map(fn (Team $team) => ['name' => $team->name])
            ->values()
            ->toArray();
    }
}
