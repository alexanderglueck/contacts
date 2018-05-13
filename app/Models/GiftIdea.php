<?php

namespace App\Models;

use App\Tenant\Traits\ForTenants;
use Illuminate\Database\Eloquent\Model;

class GiftIdea extends Model
{
    use ForTenants;

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
}
