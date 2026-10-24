<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(): Response
    {
        $activities = Activity::forCurrentTeam()
            ->with('user:id,name')
            ->latest()
            ->latest('id')
            ->paginate(50)
            ->through(function (Activity $activity) {
                // Entries of numbers, addresses etc. belong to their contact too.
                $object = null;
                if ($activity->contact_id && $contact = Contact::find($activity->contact_id)) {
                    $object = [
                        'type' => 'Contact',
                        'fullname' => $contact->fullname,
                        'ulid' => $contact->ulid,
                    ];
                }

                return [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'summary' => $activity->summary(),
                    'changes' => $activity->presentedChanges(),
                    'created_at' => optional($activity->created_at)->diffForHumans(),
                    'user' => $activity->user ? ['name' => $activity->user->name] : null,
                    'object' => $object,
                ];
            });

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
        ]);
    }
}
