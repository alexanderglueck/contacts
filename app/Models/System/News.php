<?php

namespace App\Models\System;

use App\Models\User;
use App\Interfaces\Readable;
use App\Models\Admin\Announcement;

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

    public function getReadReadables(User $user)
    {
        return $user->readNews();
    }

    public function getReadablesPivotKey()
    {
        return 'news_id';
    }
}
