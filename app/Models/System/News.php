<?php

namespace App\Models\System;

use App\Models\User;
use App\Interfaces\Readable;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Builder;

class News extends Announcement implements Readable
{
    protected $connection = 'system';

    protected $table = 'news';

    protected $fillable = [
        'title',
        'body',
        'created_at',
        'updated_at',
        'expired_at',
        'pinned_at'
    ];

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
        return $query->whereIn('id', $user->readNews()->pluck('id'));
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
        return $query->whereNotIn('id', $user->readNews()->pluck('id'));
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

        $user->readNews()->save($this);
    }

    public function isRead(Readable $readable, User $user)
    {
        return $user->readNews()
                ->where('news_id', '=', $readable->id)
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
        return News::active()->where(function ($query) use ($user) {
            return $query->whereNotIn('id', $user->readNews()->pluck('id'))
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
        return News::inactive()->orWhere(function ($query) use ($user) {
            return $query->whereIn('id', $user->readNews()->pluck('id'))
                ->whereNull('pinned_at');
        });
    }
}
