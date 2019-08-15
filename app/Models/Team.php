<?php

namespace App\Models;

use App\Tenant\Models\Tenant;
use App\Tenant\Traits\IsTenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Team extends Model implements Tenant
{
    use IsTenant;

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
        return $this->hasMany(Config::get('teamwork.invite_model'), 'team_id', 'id');
    }
}
