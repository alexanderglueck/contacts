<?php

namespace App\Models;

use App\Tenant\Models\Tenant;
use App\Tenant\Traits\IsTenant;
use App\Tenant\Traits\ForSystem;
use Illuminate\Database\Eloquent\Model;

class Team extends Model implements Tenant
{
    use IsTenant;
    use ForSystem;

    protected $casts = [
        'created' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'uuid',
        'created',
        'owner_id'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')->withTimestamps();
    }

    public function invites()
    {
        return $this->belongsToMany(User::class, 'team_invites');
    }
}
