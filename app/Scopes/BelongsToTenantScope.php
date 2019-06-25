<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class BelongsToTenantScope implements Scope
{
    /**
     * Fetch only entities that where created by the logged in user
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check()) {
             $builder->where($model->getTable() . '.team_id', '=', auth()->user()->current_team_id);
        }
    }
}
