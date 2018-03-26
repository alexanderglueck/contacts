<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Teamwork\Traits\UsedByTeams;
use Cviebrock\EloquentSluggable\Sluggable;

class Announcement extends Model
{
    use Sluggable;
    use UsedByTeams;

    protected $fillable = [
        'title',
        'body',
        'created_at',
        'updated_at'
    ];

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
