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
     * The contact's edit history, newest first. Each update lists the fields
     * that changed with their old and new value, the same diff the web shows.
     * `label` is the translated field name, so clients need no label table.
     */
    public function index(Request $request, Contact $contact): JsonResponse
    {
        $this->can('view');

        $perPage = min(max((int) $request->query('per_page', 25), 1), 100);

        $activities = $contact->activity()
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
            // created_contact -> created. Contact activities are only ever
            // created / updated / deleted.
            'event' => strtok((string) $activity->action, '_'),
            'user' => $activity->user ? ['name' => $activity->user->name] : null,
            'created_at' => optional($activity->created_at)->toIso8601String(),
            'changes' => array_map(fn (array $change) => [
                ...$change,
                'label' => $this->label($change['field']),
            ], $activity->changes()),
        ];
    }

    private function label(string $field): string
    {
        $key = 'duplicates.fields.' . $field;
        $label = __($key);

        return $label === $key ? $field : $label;
    }
}
