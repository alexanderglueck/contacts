<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;

class Announcement extends Model
{
    use Sluggable;

    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'body',
        'user_id',
        'created_at',
        'updated_at',
        'expired_at',
        'pinned_at'
    ];

    public function isPinned()
    {
        return trim($this->pinned_at) !== '';
    }

    /**
     * Returns all pinned announcements
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePinned(Builder $query)
    {
        return $query->whereNotNull('pinned_at');
    }

    /**
     * Returns all announcements that are either pinned or are not yet expired
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where(function (Builder $query) {
            return $query->whereNull('expired_at')
                ->orWhereRaw('expired_at > NOW()');
        })->orWhereNotNull('pinned_at');
    }

    /**
     * Returns all announcements that are expired
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInactive(Builder $query)
    {
        return $query
            ->whereRaw('expired_at < NOW()')
            ->whereNull('pinned_at');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['title'],
                'reserved' => ['create']
            ]
        ];
    }
}
