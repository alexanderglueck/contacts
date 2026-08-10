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
     * The registration token is preferred because FCM does not currently
     * deliver to our FIDs. Verified against production on 2026-08-11 with a
     * device that had both: sending its FID returned 404 UNREGISTERED, both in
     * the message's `token` field (which the v1 discovery doc describes as
     * accepting a FID during the transition) and in the native `fid` field via
     * raw HTTP. The same device's registration token delivered fine, so this is
     * not a stale registration.
     *
     * The FID is still stored and still used when a device has no token, so
     * this starts working on its own once FCM accepts our FIDs — but until a
     * real device receives a push addressed by FID, the token is what works and
     * the app must keep sending one.
     */
    public function pushTarget(): ?string
    {
        return $this->device_token ?: $this->fid ?: null;
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
