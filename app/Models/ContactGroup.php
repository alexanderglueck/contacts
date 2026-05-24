<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class ContactGroup extends Model
{
    use HasUlidRouteKey, Sluggable, HasFactory;

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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'reserved' => ['create', 'delete', 'edit']
            ]
        ];
    }
}
