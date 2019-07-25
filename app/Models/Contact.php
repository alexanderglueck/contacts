<?php

namespace App\Models;

use Carbon\Carbon;
use ScoutElastic\Searchable;
use App\Traits\RecordsActivity;
use App\ContactIndexConfigurator;
use App\Scopes\BelongsToTenantScope;
use App\Interfaces\CalendarInterface;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Contact extends Model implements CalendarInterface
{
    use Sluggable;
    use RecordsActivity;
    use Searchable;

    protected $fillable = [
        'firstname',
        'lastname',
        'title',
        'title_after',
        'date_of_birth',
        'iban',
        'salutation',
        'gender_id',
        'company',
        'vatin',
        'department',
        'job',
        'custom_id',
        'nickname',
        'active',
        'first_met',
        'note',
        'died_at',
        'died_from',
        'nationality_id'
    ];

    protected $attributes = [
        'active' => 1
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $indexConfigurator = ContactIndexConfigurator::class;

    protected $searchRules = [
        //
    ];

    protected $mapping = [
        'properties' => []
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
        if (trim($this->title) !== '') {
            $title = $this->title . ' ';
        }

        $titleAfter = '';
        if (trim($this->title_after) !== '') {
            $titleAfter = ', ' . $this->title_after;
        }

        $nickname = '';
        if (trim($this->nickname) !== '') {
            $nickname = ' (' . $this->nickname . ')';
        }

        $company = '';
        if (trim($this->company) !== '') {
            $company = ' // ' . $this->company;
        }

        return
            $title .
            $this->firstname . ' ' . $this->lastname .
            $titleAfter .
            $nickname .
            $company;
    }

    public function getIsAliveAttribute()
    {
        return trim($this->died_at) === '';
    }

    public function setDateOfBirthAttribute($value)
    {
        if ($value == null) {
            return;
        }

        $this->attributes['date_of_birth'] = date_create_from_format('d.m.Y', $value)
            ->format('Y-m-d');
    }

    public function setDiedAtAttribute($value)
    {
        if ($value == null) {
            return;
        }

        $this->attributes['died_at'] = date_create_from_format('d.m.Y', $value)
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
            $value = date_create_from_format('Y-m-d', $this->date_of_birth);

            return $value->format('d.m.Y');
        }

        return '';
    }

    /**
     * Returns a string in d.m.Y or d.m. format depending
     *
     * @return string A d.m.Y formatted date
     */
    public function getFormattedDiedAtAttribute()
    {
        if ($this->died_at) {
            $value = date_create_from_format('Y-m-d', $this->died_at);

            return $value->format('d.m.Y');
        }

        return '';
    }

    public function getThreadedComments()
    {
        return $this->comments()->with('owner')->get()->threaded();
    }

    public static function datesInRange(
        \DateTimeInterface $startDate, \DateTimeInterface $endDate
    ) {
        $from = $startDate->format('md');
        $to = $endDate->format('md');

        return self::select('contacts.*')
            ->whereRaw(
                "(
                    DATE_FORMAT(date_of_birth, '%m%d') BETWEEN ? AND ?
                )",
                [$from, $to]
            )
            ->where('active', 1)
            ->whereNotNull('date_of_birth')
            ->get();
    }

    public function getCalculatedName($year)
    {
        $eventDate = date_create_from_format('Y-m-d', $this->date_of_birth);
        $yearDifference = $year - $eventDate->format('Y');

        /**
         * Only calculate the x. appearance of the event if it is not the first
         * time and if the year is not hidden.
         */
        if ($yearDifference == 0) {
            $title = trans('ui.date_of_birth');
        } else {
            $title = ($year - $eventDate->format('Y')) . '. ' . trans('ui.date_of_birth');
        }

        return $title . PHP_EOL . $this->fullname;
    }

    public function getCalendarEventUrl()
    {
        return route('contacts.show', [$this->slug]);
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
     * Only return alive contacts
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeAlive($query)
    {
        return $query->whereNull('died_at');
    }

    /**
     * Only return dead contacts
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeDead($query)
    {
        return $query->whereNotNull('died_at');
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

    /**
     * Defines the has-many relationship with the ContactDate model
     *
     * @return mixed
     */
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
     * Defines the has-many relationship with the Comment model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Defines the has-many relationship with the GiftIdea model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function giftIdeas()
    {
        return $this->hasMany(GiftIdea::class);
    }

    /**
     * Defines the has-many relationship with the ContactNote model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(ContactNote::class);
    }

    /**
     * Defines the has-many relationship with the ContactCall model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function calls()
    {
        return $this->hasMany(ContactCall::class);
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
                'reserved' => ['create', 'export', 'import']
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

        $array['emails'] = $this->emails->map(function ($data) {
            return [
                'name' => $data['name'],
                'email' => $data['email']
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

        $array['notes'] = $this->notes->map(function ($data) {
            return [
                'name' => $data['name'],
                'note' => $data['note']
            ];
        })->toArray();

        $array['urls'] = $this->urls->map(function ($data) {
            return [
                'name' => $data['name'],
                'url' => $data['url']
            ];
        })->toArray();

        $array['gender'] = Gender::find($array['gender_id'])->gender;

        unset($array['generate_name']);

        return $array;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new BelongsToTenantScope());

        static::creating(function ($contact) {
            $contact->team_id = auth()->user()->current_team_id;
            $contact->created_by = auth()->id();
            $contact->updated_by = auth()->id();
        });

        static::updating(function ($contact) {
            $contact->updated_by = auth()->id();
        });
    }
}
