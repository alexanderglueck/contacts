<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use App\Models\Concerns\RendersMarkdown;
use Illuminate\Database\Eloquent\Model;

class ContactNote extends Model
{
    use HasUlidRouteKey;
    use RendersMarkdown;

    protected $fillable = ['name', 'note'];

    public function getNoteHtmlAttribute(): string
    {
        return $this->renderMarkdown($this->note);
    }

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['contact'];

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
            $model->updated_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
