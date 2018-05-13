<?php

namespace App\Models;

use App\Tenant\Traits\ForSystem;
use App\Tenant\Traits\ForTenants;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use ForSystem;

    const MALE = 1;
    const FEMALE = 2;

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
