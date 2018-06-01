<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
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
