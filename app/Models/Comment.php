<?php

namespace App\Models;

use App\Collections\CommentCollection;
use App\Models\Concerns\HasUlidRouteKey;
use App\Models\Concerns\RendersMarkdown;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasUlidRouteKey;
    use RendersMarkdown;

    protected $fillable = [
        'created_by',
        'comment',
        'parent_id',
        'created_at',
        'updated_at',
        'tombstoned_at',
        'contact_id',
    ];

    protected $casts = [
        'tombstoned_at' => 'datetime',
    ];

    public function getCommentHtmlAttribute(): string
    {
        return $this->renderMarkdown($this->comment);
    }

    public function isTombstoned(): bool
    {
        return $this->tombstoned_at !== null;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
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
