<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Adds an opaque, non-enumerable `ulid` column for URL routing while keeping
 * the integer `id` as the primary key for joins and foreign keys.
 */
trait HasUlidRouteKey
{
    protected static function bootHasUlidRouteKey(): void
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}
