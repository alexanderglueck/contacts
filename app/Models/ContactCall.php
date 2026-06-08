<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use App\Models\Concerns\RendersMarkdown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ContactCall extends Model
{
    use HasUlidRouteKey;
    use RendersMarkdown;

    protected $fillable = ['note', 'called_at'];

    // Stored in UTC (the app timezone). Clients send/receive ISO-8601 and
    // convert to/from their local zone themselves — the server never renders a
    // local time-of-day. Carbon parses an incoming Z/offset string to the
    // correct UTC instant; a naive value is treated as already-UTC.
    protected $casts = ['called_at' => 'datetime'];

    // Normalize any incoming value to UTC before storing. Laravel's datetime
    // cast alone would drop an explicit offset (it keeps the wall-clock and
    // assumes the app tz), so we parse here: Carbon::parse honors a Z/offset
    // and a naive value is read as already-UTC. The cast handles the read side.
    public function setCalledAtAttribute($value): void
    {
        $this->attributes['called_at'] = $value === null || $value === ''
            ? null
            : Carbon::parse($value)->utc()->format('Y-m-d H:i:s');
    }

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
