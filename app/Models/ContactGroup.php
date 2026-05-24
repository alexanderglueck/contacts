<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use HasUlidRouteKey, HasFactory;

    protected $fillable = ['name', 'parent_id', 'created_by', 'updated_by'];

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
