<?php

namespace App\Models;

use App\Tenant\Traits\ForSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Plan extends Model
{
    use ForSystem;

    protected $casts = [
        'teams_enabled' => 'boolean',
        'active' => 'boolean'
    ];

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active', true);
    }

    public function scopeForUsers(Builder $builder)
    {
        return $builder->where('teams_enabled', false);
    }

    public function scopeForTeams(Builder $builder)
    {
        return $builder->where('teams_enabled', true);
    }

    public function isForTeams()
    {
        return $this->teams_enabled;
    }
}
