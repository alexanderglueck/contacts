<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class GiftIdea extends Model
{
    protected $fillable = [
        'contact_id',
        'name',
        'description',
        'url',
        'due_at'
    ];

    public function setDueAtAttribute($value)
    {
        if ($value == null) {
            return;
        }

        $this->attributes['due_at'] = date_create_from_format('d.m.Y', $value)
            ->format('Y-m-d');
    }

    /**
     * Returns a string in d.m.Y format
     *
     * @return string A d.m.Y formatted date
     */
    public function getFormattedDueAtAttribute()
    {
        if ($this->due_at) {
            $value = date_create_from_format('Y-m-d', $this->due_at);

            return $value->format('d.m.Y');
        }

        return '';
    }

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
