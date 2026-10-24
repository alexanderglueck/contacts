<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
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
     * Fillable fields that differ between the "old" and "new" snapshot in
     * the body, as [field, old, new]. Empty for anything but updates.
     *
     * @return array<int, array{field: string, old: mixed, new: mixed}>
     */
    public function changes(): array
    {
        if (! str_starts_with((string) $this->action, 'updated_') || ! class_exists((string) $this->object_type)) {
            return [];
        }

        $body = json_decode((string) $this->body, true);
        $old = $body['old'] ?? [];
        $new = $body['new'] ?? [];
        $subject = new $this->object_type;

        $changes = [];
        foreach ($subject->getFillable() as $field) {
            if (! array_key_exists($field, $new)) {
                continue;
            }

            $before = $this->normalizeChangeValue($old[$field] ?? null);
            $after = $this->normalizeChangeValue($new[$field]);

            if ($before === $after) {
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
