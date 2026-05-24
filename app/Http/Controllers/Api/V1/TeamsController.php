<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentId = $user->currentTeam?->id;

        return response()->json([
            'teams' => $user->teams->map(fn (Team $team) => [
                'uuid' => $team->uuid,
                'name' => $team->name,
                'is_owner' => $user->isOwnerOfTeam($team),
                'is_current' => $currentId === $team->id,
            ])->values(),
        ]);
    }

    public function switchTo(Request $request, Team $team): JsonResponse
    {
        $user = $request->user();

        // Membership check — Eloquent's RouteModelBinding resolves any UUID
        // that exists; we still have to gate access to *this user's* teams.
        if (! $user->teams->contains('id', $team->id)) {
            abort(403, 'You are not a member of that team.');
        }

        // Same model method the web TeamController calls. Updates
        // users.current_team_id, which is the single source of truth for
        // "what tenant does this user belong to right now" — picked up by
        // SetTenant middleware on the next tenant-scoped request (and by
        // $user->currentTeam everywhere else).
        $user->switchTeam($team);

        return response()->json([
            'current_team' => [
                'uuid' => $team->uuid,
                'name' => $team->name,
            ],
        ]);
    }
}
