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
     * The FID wins. Both identifiers go into the message's `token` field —
     * kreait has no FID target, and FCM's v1 API accepts a FID there, which was
     * verified on 2026-08-12 by delivering to a real installation registered
     * through FirebaseMessaging.register(). The earlier 404 UNREGISTERED
     * results came from addressing a Firebase Installations ID that had never
     * been registered for messaging, not from the field it travelled in.
     *
     * The token stays stored and is used for devices that have no FID (older
     * app builds). Once a device reports a FID it has switched registration
     * mode, and its token can no longer be refreshed by the app — so preferring
     * the token past that point would address a value that quietly rots.
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
