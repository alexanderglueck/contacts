<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use Sluggable;

    protected $fillable = [
        'firstname',
        'lastname',
        'title',
        'title_after',
        'date_of_birth',
        'iban',
        'salutation',
        'gender_id',
        'is_company',
        'company',
        'department',
        'job',
        'custom_id',
        'nickname',
        'active'
    ];

    protected $attributes = [
        'active' => 1
    ];

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

        $company = '';
        if (trim($this->company) !== "") {
            $company = ' // ' . $this->company;
        }

        return
            $title .
            $this->firstname . ' ' . $this->lastname .
            $titleAfter .
            $nickname .
            $company;
    }

    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = date_create_from_format("d.m.Y", $value)
            ->format('Y-m-d');
    }

    /**
     * Returns a string in d.m.Y or d.m. format depending on
     * the skip_year variable.
     *
     * @return string A d.m.Y or d.m. formatted date
     */
    public function getFormattedDateOfBirthAttribute()
    {
        if ($this->date_of_birth) {
            $value = date_create_from_format("Y-m-d", $this->date_of_birth);

            return $value->format('d.m.Y');
        }

        return "";
    }


    /**
     * Sorts by lastname and firstname
     *
     * @param $query
     *
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
     *
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
     *
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
        return $this->belongsTo(User::class);
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
     * Defines the has-many relationship with the ContactUrl model
     *
     * @return mixed
     */
    public function urls()
    {
        return $this->hasMany(ContactUrl::class)->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactNumber model
     *
     * @return mixed
     */
    public function numbers()
    {
        return $this->hasMany(ContactNumber::class)->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactEmail model
     *
     * @return mixed
     */
    public function emails()
    {
        return $this->hasMany(ContactEmail::class)->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactDate model
     *
     * @return mixed
     */
    public function dates()
    {
        return $this->hasMany(ContactDate::class)->orderBy('name');
    }

    public function contactDates()
    {
        return $this->hasMany(ContactDate::class)->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactAddress model
     *
     * @return mixed
     */
    public function addresses()
    {
        return $this->hasMany(ContactAddress::class)->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the ContactGroup model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contactGroups()
    {
        return $this->belongsToMany(ContactGroup::class);
    }

    /**
     * Defines the has-many relationship with the Gender model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gender()
    {
        return $this->belongsTo(Gender::class);
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
                'source' => ['lastname', 'firstname', 'company'],
                'reserved' => ['create', 'delete', 'edit', 'inactive', 'export']
            ]
        ];
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array['dates'] = $this->contactDates->map(function ($data) {
            return [
                'name' => $data['name'],
                'date' => $data['skip_year'] ?
                    (new Carbon($data['date']))->format('d.m.')
                    : (new Carbon($data['date']))->format('d.m.Y')
            ];
        })->toArray();

        $array['numbers'] = $this->numbers->map(function ($data) {
            return [
                'name' => $data['name'],
                'number' => $data['number']
            ];
        })->toArray();

        $array['addresses'] = $this->addresses->map(function ($data) {
            return [
                'name' => $data['name'],
                'street' => $data['street'],
                'zip' => $data['zip'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => Country::find($data['country_id'])->country,
            ];
        })->toArray();


        $array['gender'] = Gender::find($array['gender_id'])->gender;

        unset($array['generate_name']);

        return $array;
    }
}
