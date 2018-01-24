<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use Sluggable;


    protected $fillable = ['name', 'parent_id', 'created_by', 'updated_by'];


    /**
     * Sort the ContactGroups by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('name');
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

    /**
     * Defines the has-many relationship with the Contact model
     * Returns only active contacts sorted by the contact sorted scope
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->belongsToMany('App\Models\Contact')->sorted()->active();
    }
}
