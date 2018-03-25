<?php

namespace App\Models;

use Mpociot\Teamwork\Traits\UsedByTeams;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use Sluggable;
    use UsedByTeams;

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
                'source' => ['name'],
                'reserved' => ['create']
            ]
        ];
    }
}
