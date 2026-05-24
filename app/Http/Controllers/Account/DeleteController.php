<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class DeleteController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('UserSettings/Delete', [
            'lastMemberTeams' => $user ? $this->teamsToCascadeDelete($user) : [],
            'coOwnedTeams' => $user ? $this->teamsBlockingDelete($user) : [],
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // teams.owner_id is FK RESTRICT — we can't force-delete the user while
        // they still own any team. Block when ownership transfer is the only
        // sensible answer (team owned by us, other members still in it).
        if (! empty($this->teamsBlockingDelete($user))) {
            Session::flash('alert-danger', 'You still own teams that have other members. Transfer ownership before deleting your account.');

            return redirect()->route('user_settings.delete.show');
        }

        if ($user->subscribed('main')) {
            $user->subscription('main')->cancel();
        }

        // Sole-member teams: delete the team itself, which cascades to
        // contacts, roles, team_user, team_invites via the migration FKs.
        DB::transaction(function () use ($user) {
            $soloTeamIds = $user->teams
                ->filter(fn (Team $team) => $team->users()
                    ->where('users.id', '!=', $user->id)
                    ->count() === 0)
                ->pluck('id');

            Team::whereIn('id', $soloTeamIds)->delete();

            if ($user->image && file_exists(storage_path('app/public/').$user->image)) {
                unlink(storage_path('app/public/').$user->image);
            }

            $user->forceDelete();
        });

        Auth::logout();

        Session::flash('alert-success', trans('flash_message.user_setting.deleted'));

        return redirect()->route('login');
    }

    /**
     * Teams that will be deleted along with the user (sole active member).
     *
     * @return array<int, array{name: string}>
     */
    private function teamsToCascadeDelete(User $user): array
    {
        return $user->teams
            ->filter(fn (Team $team) => $team->users()
                ->where('users.id', '!=', $user->id)
                ->count() === 0)
            ->map(fn (Team $team) => ['name' => $team->name])
            ->values()
            ->toArray();
    }

    /**
     * Teams owned by this user that still have other active members.
     * These prevent deletion until ownership is transferred (FK RESTRICT
     * on teams.owner_id would otherwise throw on forceDelete).
     *
     * @return array<int, array{name: string, members: int}>
     */
    private function teamsBlockingDelete(User $user): array
    {
        return $user->teams
            ->filter(fn (Team $team) => $team->owner_id === $user->id
                && $team->users()->where('users.id', '!=', $user->id)->exists())
            ->map(fn (Team $team) => [
                'name' => $team->name,
                'members' => $team->users()->where('users.id', '!=', $user->id)->count(),
            ])
            ->values()
            ->toArray();
    }
}
