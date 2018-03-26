<?php

namespace App\Models;

use App\Scopes\CreatedByScope;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ContactAddress extends Model
{
    use Sluggable;

    protected $fillable = [
        'contact_id',
        'name',
        'street',
        'zip',
        'city',
        'state',
        'country_id',
        'longitude',
        'latitude',
        'created_by',
        'updated_by'
    ];

    protected $attributes = [
        'country_id' => 164
    ];

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Defines the has-many relationship with the Country model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
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
                'reserved' => ['create']
            ],

        ];
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CreatedByScope());
    }
}
