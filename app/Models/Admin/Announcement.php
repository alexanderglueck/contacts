<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Interfaces\Readable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Cviebrock\EloquentSluggable\Sluggable;

class Announcement extends Model implements Readable
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
     * Returns all announcements that are either pinned or have not yet expired
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
     * Returns all announcements that are expired and not pinned
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
     * All read announcements
     *
     * @param Builder $query
     * @param User    $user
     *
     * @return Builder
     */
    public function scopeRead(Builder $query, User $user)
    {
        return $query->whereIn('id', $user->readAnnouncements()->pluck('id'));
    }

    /**
     * Return all unread announcements
     *
     * @param Builder $query
     * @param User    $user
     *
     * @return Builder
     */
    public function scopeUnread(Builder $query, User $user)
    {
        return $query->whereNotIn('id', $user->readAnnouncements()->pluck('id'));
    }

    /**
     * Marks an announcement as read
     *
     * @param User $user
     */
    public function markAsRead(User $user)
    {
        if ($this->isRead($this, $user)) {
            return;
        }

        $user->readAnnouncements()->save($this);
    }

    public function isRead(Readable $readable, User $user)
    {
        return $user->readAnnouncements()
                ->where('announcement_id', '=', $readable->id)
                ->count() == 1;
    }

    /**
     * Fetch the active announcements have not been read or that are pinned
     *
     * @param User $user
     *
     * @return mixed
     */
    public static function displayed(User $user)
    {
        return self::active()->where(function ($query) use ($user) {
            return $query->whereNotIn('id', $user->readAnnouncements()->pluck('id'))
                ->orWhereNotNull('pinned_at');
        });
    }

    /**
     * Returns all announcements that have expired or have been read
     *
     * @param User $user
     *
     * @return mixed
     */
    public static function hidden(User $user)
    {
        return self::inactive()->orWhere(function ($query) use ($user) {
            return $query->whereIn('id', $user->readAnnouncements()->pluck('id'))
                ->whereNull('pinned_at');
        });
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
