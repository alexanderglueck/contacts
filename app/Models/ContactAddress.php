<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class ContactAddress extends Model
{
    use HasUlidRouteKey, HasFactory, RecordsActivity;

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
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['contact'];

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * Human-readable value for the field diff in the activity log.
     */
    public function formatActivityValue(string $field, ?string $value): ?string
    {
        if ($value !== null && $field === 'country_id') {
            return Country::find($value)?->country ?? $value;
        }

        return $value;
    }

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
