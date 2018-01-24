<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class ContactNumber extends Model
{
    use Sluggable;

    protected $fillable = ['name', 'number', 'created_by', 'updated_by'];

    /**
     * Returns a formatted string of numbers.
     * Any other characters are discarded from the number property
     *
     * @return string A string consisting of numbers
     */
    public function getFormattedNumberAttribute()
    {
        return preg_replace("/[^0-9+]/", "", $this->number);
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo('App\Models\Contact');
    }
}
