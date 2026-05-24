<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use App\Scopes\BelongsToTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use HasUlidRouteKey, HasFactory;

    protected $fillable = ['name', 'parent_id', 'created_by', 'updated_by'];

    protected static function boot(): void
    {
        parent::boot();

        // BelongsToTenantScope filters reads by team_id = current_team_id
        // whenever a user is authenticated. Combined with the creating
        // hook below, this means a contact group only ever leaves its
        // tenant if some code explicitly bypasses the scope.
        static::addGlobalScope(new BelongsToTenantScope());

        static::creating(function (self $group) {
            if (auth()->check() && $group->team_id === null) {
                $group->team_id = auth()->user()->current_team_id;
            }
        });
    }

    /**
     * Sort the ContactGroups by name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Defines the has-many relationship with the Contact model
     * Returns only active contacts sorted by the contact sorted scope
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class)->sorted()->active();
    }
}
