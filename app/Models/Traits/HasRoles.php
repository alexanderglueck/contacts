<?php

namespace App\Models\Traits;

use App\Models\Role;

trait HasRoles
{
    use HasPermissions;

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole($role)
    {
        $this->roles()->attach($role);

        $this->forgetCachedPermissions();
    }

    public function removeRole($role)
    {
        $this->roles()->detach($role);

        $this->forgetCachedPermissions();
    }

    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }
}
