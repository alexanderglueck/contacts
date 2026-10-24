<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactActivitiesController extends Controller
{
    protected ?string $accessEntity = 'activities';

    /**
     * The contact's edit history, newest first, including its numbers,
     * emails, URLs, addresses and dates. Each entry lists the fields that
     * changed with their old and new value, the same diff the web shows.
     * `summary` and `label` come translated, so clients need no label table.
     */
    public function index(Request $request, Contact $contact): JsonResponse
    {
        $this->can('view');

        $perPage = min(max((int) $request->query('per_page', 25), 1), 100);

        $activities = Activity::query()
            ->where('team_id', $contact->team_id)
            ->where('contact_id', $contact->id)
            ->with('user:id,name')
            ->latest()
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'data' => $activities->getCollection()->map(fn (Activity $a) => $this->serialize($a))->values(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    private function serialize(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'event' => $activity->event(),
            'subject' => $activity->subjectName(),
            'summary' => $activity->summary(),
            'user' => $activity->user ? ['name' => $activity->user->name] : null,
            'created_at' => optional($activity->created_at)->toIso8601String(),
            'changes' => $activity->presentedChanges(),
        ];
    }
}
