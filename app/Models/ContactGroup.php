<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Teamwork\Traits\UsedByTeams;
use Cviebrock\EloquentSluggable\Sluggable;

class ContactGroup extends Model
{
    use Sluggable;
    use UsedByTeams;

    protected $fillable = ['name', 'parent_id', 'created_by', 'updated_by'];

    /**
     * Sort the ContactGroups by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the Contact model
     * Returns only active contacts sorted by the contact sorted scope
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class)->sorted()->active();
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
                'source' => 'name',
                'reserved' => ['create', 'delete', 'edit']
            ]
        ];
    }
}
