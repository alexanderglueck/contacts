<?php

namespace App\Models;

use App\Tenant\Traits\ForTenants;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use Sluggable;
    use ForTenants;

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
}
