<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

/**
 * Sends a notification as a Firebase Cloud Messaging push to every device
 * token returned by the notifiable's routeNotificationForFcm().
 *
 * Pushes are sent data-only (no notification block) at high Android message
 * priority. Data-only is what lets the app's onMessageReceived run in every
 * app state — including killed/background — so it can render the notification
 * itself (controlling grouping/stacking) and deep-link taps to the calendar.
 * A notification block would otherwise be intercepted by the system tray and
 * skip onMessageReceived. High priority earns the Doze delivery exemption that
 * data-only messages need to arrive on time. title/body ride along in the data
 * payload for the app to display.
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

        $data = array_merge($payload['data'] ?? [], [
            'title' => $payload['title'] ?? config('app.name'),
            'body' => $payload['body'] ?? '',
        ]);

        foreach ((array) $tokens as $token) {
            $message = CloudMessage::new()
                ->withToken($token)
                ->withData($data)
                ->withHighestPossiblePriority();

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
