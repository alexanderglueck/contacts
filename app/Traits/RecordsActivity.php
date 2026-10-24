<?php

namespace App\Traits;

use ReflectionClass;
use App\Models\Activity;
use App\Models\Contact;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    /**
     * Fetch all model events that require activity recording.
     *
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        return ['created', 'updated', 'deleted'];
    }

    /**
     * Fields that never make an update worth recording on their own, e.g. the
     * timestamp bump when a child touches its contact, or geocoding results.
     *
     * @return array<int, string>
     */
    public function activityIgnoredFields(): array
    {
        return ['created_at', 'updated_at', 'created_by', 'updated_by', 'contact_id', 'team_id', 'latitude', 'longitude'];
    }

    /**
     * Record new activity for the model.
     *
     * @param string $event
     */
    protected function recordActivity($event)
    {
        if ($event === 'updated' && empty(array_diff(array_keys($this->getChanges()), $this->activityIgnoredFields()))) {
            return;
        }

        $contactId = $this instanceof Contact ? $this->id : $this->contact_id;
        $teamId = $this instanceof Contact
            ? $this->team_id
            : Contact::withoutGlobalScopes()->whereKey($contactId)->value('team_id');

        $this->activity()->create([
            'user_id' => auth()->id(),
            'team_id' => $teamId,
            'contact_id' => $contactId,
            'action' => $this->getActivityType($event),
            'body' => json_encode([
                'old' => $this->original,
                'new' => $this
            ])
        ]);
    }

    /**
     * Fetch the activity relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'object');
    }

    /**
     * Determine the activity type.
     *
     * @param  string $event
     *
     * @return string
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
