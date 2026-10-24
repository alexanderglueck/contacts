<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    /**
     * Short subject names for the models that record activity, used in the
     * API payload and in the translation keys under activity.summary/fields.
     */
    private const SUBJECTS = [
        Contact::class => 'contact',
        ContactNumber::class => 'number',
        ContactEmail::class => 'email',
        ContactUrl::class => 'url',
        ContactAddress::class => 'address',
        ContactDate::class => 'date',
    ];

    private const BOOLEAN_FIELDS = ['active', 'skip_year'];

    protected $fillable = [
        'user_id',
        'team_id',
        'contact_id',
        'action',
        'body'
    ];

    /**
     * Fetch the associated subject for the activity.
     *
     * @return MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Only the signed-in user's current team. Activities carry no global
     * scope, so every listing has to go through this.
     */
    public function scopeForCurrentTeam(Builder $query): Builder
    {
        return $query->where('team_id', auth()->user()?->current_team_id);
    }

    /** "created", "updated" or "deleted". */
    public function event(): string
    {
        return strtok((string) $this->action, '_');
    }

    /** "contact", "number", "email", ... or the raw model name for anything else. */
    public function subjectName(): string
    {
        return self::SUBJECTS[$this->object_type] ?? strtolower(class_basename((string) $this->object_type));
    }

    /** Translated one-line summary, e.g. "Telefonnummer hinzugefügt". */
    public function summary(): string
    {
        $key = 'activity.summary.' . $this->event() . '.' . $this->subjectName();
        $summary = __($key);

        return $summary === $key ? (string) $this->action : $summary;
    }

    /**
     * Fillable fields worth showing, as [field, old, new]. An update lists the
     * fields that differ between the "old" and "new" snapshot in the body. A
     * created entry lists the values it was created with, a deleted entry the
     * values it had.
     *
     * @return array<int, array{field: string, old: mixed, new: mixed}>
     */
    public function changes(): array
    {
        if (! class_exists((string) $this->object_type)) {
            return [];
        }

        $body = json_decode((string) $this->body, true);
        $old = $body['old'] ?? [];
        $new = $body['new'] ?? [];
        $subject = new $this->object_type;
        $event = $this->event();
        $ignored = method_exists($subject, 'activityIgnoredFields') ? $subject->activityIgnoredFields() : [];

        $changes = [];
        foreach (array_diff($subject->getFillable(), $ignored) as $field) {
            $before = $event === 'created' ? null : $this->normalizeChangeValue($old[$field] ?? null);
            $after = $event === 'deleted' ? null : $this->normalizeChangeValue($new[$field] ?? null);

            if ($before === $after) {
                continue;
            }

            if ($event === 'updated' && ! array_key_exists($field, $new)) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'old' => method_exists($subject, 'formatActivityValue') ? $subject->formatActivityValue($field, $before) : $before,
                'new' => method_exists($subject, 'formatActivityValue') ? $subject->formatActivityValue($field, $after) : $after,
            ];
        }

        return $changes;
    }

    /**
     * changes() with a translated label per field and readable booleans, for
     * the web views and the API.
     *
     * @return array<int, array{field: string, label: string, old: mixed, new: mixed}>
     */
    public function presentedChanges(): array
    {
        return array_map(fn (array $change) => [
            'field' => $change['field'],
            'label' => $this->fieldLabel($change['field']),
            'old' => $this->presentValue($change['field'], $change['old']),
            'new' => $this->presentValue($change['field'], $change['new']),
        ], $this->changes());
    }

    private function fieldLabel(string $field): string
    {
        foreach (['activity.fields.' . $this->subjectName() . '.' . $field, 'duplicates.fields.' . $field] as $key) {
            $label = __($key);
            if ($label !== $key) {
                return $label;
            }
        }

        return $field;
    }

    private function presentValue(string $field, ?string $value): ?string
    {
        if ($value === null || ! in_array($field, self::BOOLEAN_FIELDS, true)) {
            return $value;
        }

        return $value === '1' ? __('activity.yes') : __('activity.no');
    }

    /**
     * "old" holds raw column values, "new" the cast ones (true vs 1), and
     * the form posts "" for cleared fields. Compare them on equal terms.
     */
    private function normalizeChangeValue(mixed $value): ?string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) ? (string) $value : json_encode($value);
    }
}
