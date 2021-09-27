<?php

namespace App\Models;

use App\Collections\CommentCollection;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'created_by',
        'comment',
        'parent_id',
        'created_at',
        'updated_at',
        'contact_id'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Use a custom collection for all comments.
     *
     * @param array $models
     *
     * @return CommentCollection
     */
    public function newCollection(array $models = [])
    {
        return new CommentCollection($models);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            $contact->created_by = auth()->id();
        });
    }
}
