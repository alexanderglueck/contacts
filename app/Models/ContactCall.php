<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactCall extends Model
{
    protected $fillable = ['note', 'called_at'];

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
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function setCalledAtAttribute($value)
    {
        if ($value == null) {
            return;
        }

        $this->attributes['called_at'] = date_create_from_format('d.m.Y H:i', $value)
            ->format('Y-m-d H:i:s');
    }

    /**
     * @return string A d.m.Y or d.m. formatted date
     */
    public function getFormattedCalledAtAttribute()
    {
        if ($this->called_at) {
            $value = date_create_from_format('Y-m-d H:i:s', $this->called_at);

            return $value->format('d.m.Y H:i');
        }

        return '';
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
