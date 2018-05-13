<?php

namespace App\Models;

use App\Tenant\Models\Tenant;
use App\Tenant\Traits\ForSystem;
use App\Tenant\Traits\IsTenant;
use Illuminate\Database\Eloquent\Model;

class Team extends Model implements Tenant
{
    use IsTenant;
    use ForSystem;

    protected $fillable = [
        'name',
        'uuid'
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    public function invites()
    {
        return $this->belongsToMany(User::class, 'team_invites');
    }
}

