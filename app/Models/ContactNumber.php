<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ContactNumber extends Model
{
    use HasUlidRouteKey, HasFactory;

    // `e164` is intentionally NOT fillable — it's derived from `number` by
    // the mutator below. Letting clients set it directly would mean it
    // could drift from `number`.
    protected $fillable = ['name', 'number'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['contact'];

    /**
     * Mutator: every time `number` is assigned, recompute the canonical
     * E.164 form and store it alongside. Display value (what the user
     * typed) is preserved in `number`; queries / equality go through
     * `e164`. Unparseable input stores null in `e164` — the lookup
     * controller has a digit-suffix fallback for that case.
     */
    public function setNumberAttribute(?string $value): void
    {
        $this->attributes['number'] = $value;
        $this->attributes['e164'] = $this->normaliseToE164($value);
    }

    /**
     * Returns a formatted string of numbers.
     * Any other characters are discarded from the number property
     *
     * @return string A string consisting of numbers
     */
    public function getFormattedNumberAttribute()
    {
        return preg_replace('/[^0-9+]/', '', $this->number);
    }

    private function normaliseToE164(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            $util = PhoneNumberUtil::getInstance();
            $parsed = $util->parse($value, config('contacts.phone_default_region', 'AT'));

            return $util->isValidNumber($parsed)
                ? $util->format($parsed, PhoneNumberFormat::E164)
                : null;
        } catch (NumberParseException) {
            return null;
        }
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
