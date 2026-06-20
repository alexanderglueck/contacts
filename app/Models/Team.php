<?php

namespace App\Models;

use App\Tenant\Models\Tenant;
use App\Tenant\Traits\IsTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam implements Tenant
{
    use IsTenant, HasFactory;

    protected $casts = [
        'created' => 'boolean',
        'password_reset_disabled' => 'boolean',
        'two_factor_required' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'uuid',
        'created',
        'owner_id',
        'password_reset_disabled',
        'two_factor_required',
    ];

    /**
     * This app tracks team ownership via `owner_id`, not Jetstream's default
     * `user_id`, so override the inherited owner() relation accordingly.
     */
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
        return $this->hasMany(TeamInvite::class, 'team_id', 'id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
