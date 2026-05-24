<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(): Response
    {
        $activities = Activity::with('user:id,name')->latest()->get();

        $items = $activities->map(function (Activity $activity) {
            $object = null;
            if ($activity->object_type && $activity->object_id) {
                $model = $activity->object_type::find($activity->object_id);
                if ($model) {
                    $object = [
                        'type' => class_basename($activity->object_type),
                        'fullname' => $model->fullname ?? null,
                        'ulid' => $model->ulid ?? null,
                    ];
                }
            }

            return [
                'id' => $activity->id,
                'action' => $activity->action,
                'body' => $activity->body,
                'created_at' => optional($activity->created_at)->diffForHumans(),
                'user' => $activity->user ? ['name' => $activity->user->name] : null,
                'object' => $object,
            ];
        });

        return Inertia::render('Activities/Index', [
            'activities' => $items,
        ]);
    }
}
