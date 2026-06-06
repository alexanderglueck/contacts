<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

/**
 * Sends a notification as a Firebase Cloud Messaging push to every device
 * token returned by the notifiable's routeNotificationForFcm(). Pushes go out
 * at the lowest possible priority (these are non-urgent daily reminders).
 *
 * Per-device delivery failures are logged and swallowed so one stale token
 * can't abort the whole batch — mirrors url-pusher's SendUrlToDevice.
 */
class FcmChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $tokens = $notifiable->routeNotificationFor('fcm', $notification);

        if (empty($tokens)) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        foreach ((array) $tokens as $token) {
            $message = CloudMessage::new()
                ->withToken($token)
                ->withNotification([
                    'title' => $payload['title'] ?? config('app.name'),
                    'body' => $payload['body'] ?? '',
                ])
                ->withData($payload['data'] ?? [])
                ->withLowestPossiblePriority();

            try {
                Firebase::messaging()->send($message);
            } catch (FirebaseException $e) {
                Log::warning('FCM push failed', [
                    'token' => substr((string) $token, 0, 12).'…',
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
