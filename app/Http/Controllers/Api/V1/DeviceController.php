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
     * Register a device (name + Firebase token) for the authenticated user.
     *
     * Upserts by token: re-posting a token the user already has (e.g. after the
     * app clears its data while FCM keeps the same registration token) updates
     * that row's name instead of creating a duplicate — which would otherwise
     * cause the same device to be pushed to twice.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'token' => ['required', 'string', 'max:255'],
        ]);

        $device = $request->user()->devices()->firstOrNew([
            'device_token' => $validated['token'],
        ]);

        $wasNew = ! $device->exists;

        $device->name = $validated['name'];
        $device->device_token = $validated['token'];
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
