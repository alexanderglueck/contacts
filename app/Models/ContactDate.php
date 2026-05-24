<?php

namespace App\Models;

use App\Interfaces\CalendarInterface;
use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContactDate extends Model implements CalendarInterface
{
    use HasUlidRouteKey, HasFactory;

    protected $fillable = ['name', 'date', 'skip_year'];

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
        if ($value === null || trim((string) $value) === '') {
            $this->attributes['date'] = null;

            return;
        }

        $date = date_create_from_format('Y-m-d', $value)
            ?: date_create_from_format('d.m.Y', $value);

        if ($date) {
            $this->attributes['date'] = $date->format('Y-m-d 00:00:00');
        }
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
        \DateTimeInterface $startDate, \DateTimeInterface $endDate
    ) {
        $query = self::select('contact_dates.*')
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1);

        // FullCalendar's listYear view requests start..end spanning a full
        // year, which collapses both MMDDs to the same value and makes the
        // BETWEEN match exactly one day. When the range is 365+ days every
        // MMDD qualifies, so skip the filter entirely.
        if ($startDate->diff($endDate)->days >= 365) {
            return $query->get();
        }

        $from = $startDate->format('md');
        $to = $endDate->format('md');
        $mmdd = self::mmddExpression('date');

        return $query
            ->whereRaw(
                "(
                    {$mmdd} BETWEEN ? AND ?
                    OR (
                        ? > ?
                        AND (
                            {$mmdd} >= ?
                            OR {$mmdd} <= ?
                        )
                    )
                )",
                [$from, $to, $from, $to, $from, $to]
            )
            ->get();
    }

    public function getCalendarEventUrl()
    {
        return route('contacts.show', $this->contact->ulid);
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
        $mmdd = self::mmddExpression('date');

        return self::select('contact_dates.*')
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1)
            ->whereRaw("{$mmdd} = ?", [$date->format('md')])
            ->get();
    }

    /**
     * Return a SQL expression that extracts MMDD from a date column,
     * portable across MySQL and SQLite (which the test suite uses).
     */
    private static function mmddExpression(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%m%d', {$column})"
            : "DATE_FORMAT({$column}, '%m%d')";
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
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
