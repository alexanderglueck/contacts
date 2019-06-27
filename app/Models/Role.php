<?php

namespace App\Models;

use App\Scopes\BelongsToTenantScope;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as BaseRole;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends BaseRole
{
    use Sluggable;

    public function syncUsers($users)
    {
        $currentUsers = $this->users;

        foreach ($currentUsers as $user) {
            /** @var $user User */
            $user->removeRole($this->id);
        }

        if (is_array($users)) {
            foreach ($users as $user) {
                User::find($user)->assignRole($this->id);
            }
        }
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions')
        );
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            'model_id'
        );
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['name'],
                'reserved' => ['create']
            ]
        ];
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new BelongsToTenantScope());
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        if (static::withoutGlobalScopes()
            ->where('team_id', $attributes['team_id'])
            ->where('name', $attributes['name'])
            ->where('guard_name', $attributes['guard_name'])
            ->first()
        ) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        if (isNotLumen() && app()::VERSION < '5.4') {
            return parent::create($attributes);
        }

        return static::query()->create($attributes);
    }
}
