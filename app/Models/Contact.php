<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use Sluggable;

    protected $fillable = [
        'lastname',
        'firstname',
        'company',
        'job',
        'department',
        'title',
        'title_after',
        'salutation',
        'gender_id',
        'nickname',
        'active'
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
                'source' => ['lastname', 'firstname'],
                'reserved' => ['create', 'delete', 'edit', 'inactive', 'export']
            ]
        ];
    }

    /**
     * Returns the full name of a contact including a title (if given),
     * first, lastname, title_after (if given) and nickname (if given)
     *
     * @return string The full display name of a Contact
     */
    public function getFullnameAttribute()
    {
        $title = '';
        if (trim($this->title) !== "") {
            $title = $this->title . ' ';
        }

        $titleAfter = '';
        if (trim($this->title_after) !== "") {
            $titleAfter = ', ' . $this->title_after;
        }

        $nickname = '';
        if (trim($this->nickname) !== "") {
            $nickname = ' (' . $this->nickname . ')';
        }

        return
            $title .
            $this->firstname . ' ' . $this->lastname .
            $titleAfter .
            $nickname;
    }

    /**
     * Sorts by lastname and firstname
     *
     * @param $query
     * @return mixed
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('lastname')->orderBy('firstname');
    }

    /**
     * Only return active contacts
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Only return inactive contacts
     *
     * @param $query
     * @return mixed
     */
    public function scopeNotActive($query)
    {
        return $query->where('active', 0);
    }

    /**
     * Defines the has-many relationship with the User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Defines the has-many relationship with the Country model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    /**
     * Defines the has-many relationship with the ContactUrl model
     *
     * @return mixed
     */
    public function urls()
    {
        return $this->hasMany('App\Models\ContactUrl')->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactNumber model
     *
     * @return mixed
     */
    public function numbers()
    {
        return $this->hasMany('App\Models\ContactNumber')->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactEmail model
     *
     * @return mixed
     */
    public function emails()
    {
        return $this->hasMany('App\Models\ContactEmail')->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactDate model
     *
     * @return mixed
     */
    public function dates()
    {
        return $this->hasMany('App\Models\ContactDate')->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactAddress model
     *
     * @return mixed
     */
    public function addresses()
    {
        return $this->hasMany('App\Models\ContactAddress')->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactGroup model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contactGroups()
    {
        return $this->belongsToMany('App\Models\ContactGroup');
    }

    /**
     * Defines the has-many relationship with the Gender model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gender()
    {
        return $this->belongsTo('App\Models\Gender');
    }
}
