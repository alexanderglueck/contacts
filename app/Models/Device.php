<?php

namespace App\Models;

use App\Models\Concerns\HasUlidRouteKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's registered device that can receive push notifications.
 *
 * Two identifiers can address a device. The fid is the Firebase Installation
 * ID, which is stable across token rotations and is what current app builds
 * register with; device_token is the legacy Firebase Cloud Messaging
 * registration token, kept for older builds and as a rollback value.
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

    /**
     * The value to address this device with when sending a push, or null when
     * it can't be pushed to at all.
     *
     * Both go into the message's `token` field: kreait/firebase-php has no FID
     * target (MessageTarget only knows condition/token/topic), and FCM's v1 API
     * documents `token` as accepting a FID during the transition period. The
     * FID is preferred so that devices which have stopped sending a token keep
     * working, and so any problem with FID delivery surfaces while the token is
     * still stored as a fallback.
     */
    public function pushTarget(): ?string
    {
        return $this->fid ?: $this->device_token ?: null;
    }

    /**
     * Devices that can actually be pushed to — those with either identifier.
     */
    public function scopeWithPushTarget(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNotNull('fid')->orWhereNotNull('device_token');
        });
    }
}
