<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Server-side device registration for push notifications. The mobile app
 * (later) posts its FCM token here; for now the endpoints let an authenticated
 * user register, list and remove devices.
 */
class DeviceController extends Controller
{
    /**
     * List the authenticated user's registered devices.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return DeviceResource::collection($request->user()->devices);
    }

    /**
     * Register a device for the authenticated user.
     *
     * Takes a name plus at least one identifier: the Firebase Installation ID
     * (fid) that current app builds send, and/or the legacy FCM registration
     * token. Current builds send both, older ones only a token.
     *
     * Matching an existing row is deliberately fid-first-then-token rather than
     * fid-only. On an upgraded install's first call we get a token we already
     * know plus a FID we've never seen: a fid-only lookup would miss and insert
     * a second row, leaving the original to be pushed to as well. Falling back
     * to the token finds that row and adopts the FID onto it, which is what
     * backfills FIDs as devices check in — no data migration needed.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'token' => ['nullable', 'string', 'max:255', 'required_without:fid'],
            'fid' => ['nullable', 'string', 'max:255', 'required_without:token'],
        ]);

        $fid = $validated['fid'] ?? null;
        $token = $validated['token'] ?? null;

        $user = $request->user();

        $device = ($fid ? $user->devices()->where('fid', $fid)->first() : null)
            ?? ($token ? $user->devices()->where('device_token', $token)->first() : null)
            ?? $user->devices()->make();

        $wasNew = ! $device->exists;

        $device->name = $validated['name'];

        // Only overwrite an identifier the app actually sent, so a build that
        // has dropped tokens doesn't wipe the stored fallback.
        if ($fid) {
            $device->fid = $fid;
        }

        if ($token) {
            $device->device_token = $token;
        }

        $device->save();

        return (new DeviceResource($device))
            ->response()
            ->setStatusCode($wasNew ? 201 : 200);
    }

    /**
     * Remove one of the authenticated user's devices.
     */
    public function destroy(Request $request, Device $device): JsonResponse
    {
        abort_unless($device->user_id === $request->user()->id, 403);

        $device->delete();

        return response()->json(status: 204);
    }
}
