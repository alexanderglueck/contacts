<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's registered device that can receive push notifications. The
 * device_token is the Firebase Cloud Messaging registration token.
 */
class Device extends Model
{
    use HasFactory;
    use HasUlidRouteKey;

    protected $fillable = [
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithDeviceToken(Builder $query): Builder
    {
        return $query->whereNotNull('device_token');
    }
}
