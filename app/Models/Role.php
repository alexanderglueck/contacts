<?php

namespace App\Models;

use App\Scopes\BelongsToTenantScope;
use App\Models\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Role extends Model
{
    use Sluggable, HasPermissions;

    protected $fillable = [
        'name',
        'team_id'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function syncUsers($users)
    {
        $currentUsers = $this->users;

        foreach ($currentUsers as $user) {
            /** @var $user User */
            $user->roles()->detach($this->id);
        }

        if (is_array($users)) {
            foreach ($users as $user) {
                User::find($user)->assignRole($this->id);
            }
        }
    }

    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
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
}
