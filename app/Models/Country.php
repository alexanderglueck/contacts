<?php

namespace App\Models;

use App\Tenant\Traits\ForSystem;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use ForSystem;

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
