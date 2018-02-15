<?php

namespace App\Scopes;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class CreatedByScope implements Scope
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
        $builder->where($model->getTable() . '.created_by', '=', Auth::user()->id);
    }
}
