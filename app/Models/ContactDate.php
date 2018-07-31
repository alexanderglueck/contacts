<?php

namespace App\Models;

use App\Scopes\CreatedByScope;
use App\Interfaces\CalendarInterface;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ContactDate extends Model implements CalendarInterface
{
    use Sluggable;

    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'date',
        'skip_year',
        'created_by',
        'updated_by'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['contact'];

    /**
     * Returns a string in d.m.Y or d.m. format depending on
     * the skip_year variable.
     *
     * @return string A d.m.Y or d.m. formatted date
     */
    public function getFormattedDateAttribute()
    {
        $value = date_create_from_format('Y-m-d H:i:s', $this->date);

        if ($this->skip_year) {
            return $value ? $value->format('d.m.') : '';
        } else {
            return $value ? $value->format('d.m.Y') : '';
        }
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = date_create_from_format('d.m.Y', $value)
            ->format('Y-m-d 00:00:00');
    }

    public function getCalculatedName($year)
    {
        $eventDate = date_create_from_format('Y-m-d H:i:s', $this->date);
        $yearDifference = $year - $eventDate->format('Y');

        /**
         * Only calculate the x. appearance of the event if it is not the first
         * time and if the year is not hidden.
         */
        if ($yearDifference == 0 || $this->skip_year) {
            $title = $this->name;
        } else {
            $title = ($year - $eventDate->format('Y')) . '. ' . $this->name;
        }

        return $title . PHP_EOL . $this->contact->fullname;
    }

    /**
     * Get Collection of ContactDates in given range
     *
     * @static
     *
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function datesInRange(
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ) {
        $from = $startDate->format('md');
        $to = $endDate->format('md');

        return self::select('contact_dates.*')
            ->whereRaw(
                "(
                    DATE_FORMAT(date, '%m%d') BETWEEN ? AND ?
                    OR (
                        ? > ?
                        AND (
                            DATE_FORMAT(date, '%m%d') >= ?
                            OR DATE_FORMAT(date, '%m%d') <= ?
                        )
                    )
                )",
                [$from, $to, $from, $to, $from, $to]
            )
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1)
            ->get();
    }

    public function getCalendarEventUrl()
    {
        return route('contact_dates.show', [$this->contact->slug, $this->slug]);
    }

    /**
     * Returns a collection of ContactDates for the given date
     *
     * @static
     *
     * @param \DateTime $date
     *
     * @return mixed
     */
    public static function datesOnDate(\DateTime $date)
    {
        return self::select('contact_dates.*')
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1)
            ->whereRaw("DATE_FORMAT(date, '%m%d') = ?", [$date->format('md')])
            ->get();
    }

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
            ]
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
