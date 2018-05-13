<?php

namespace App\Models;

use App\Tenant\Traits\ForTenants;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use ForTenants;

    protected $fillable = [
        'user_id',
        'action',
        'body'
    ];

    /**
     * Fetch the associated subject for the activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
