<?php

namespace App\Console\Commands\Push;

use App\Models\User;
use Illuminate\Console\Command;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;

/**
 * Sends a one-off test push to a user's registered devices to verify the
 * server-side FCM setup (credentials + stored tokens).
 *
 * Unlike the production FcmChannel, this command does NOT swallow errors — it
 * prints the Firebase failure per device so you can diagnose bad credentials
 * or stale tokens directly.
 */
class SendTestPush extends Command
{
    protected $signature = 'push:test
                            {user : User id or email}
                            {--message= : Custom body text}
                            {--device= : Limit to a single device ulid}
                            {--low-priority : Send at lowest priority, like the real reminders}';

    protected $description = 'Send a test push notification to a user\'s devices via FCM.';

    public function handle(): int
    {
        $identifier = (string) $this->argument('user');

        $user = User::where('id', $identifier)->orWhere('email', $identifier)->first();

        if (! $user) {
            $this->error("No user found for [{$identifier}].");

            return self::FAILURE;
        }

        $query = $user->devices()->withDeviceToken();

        if ($deviceUlid = $this->option('device')) {
            $query->where('ulid', $deviceUlid);
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            $this->warn("User {$user->email} has no devices with a push token".
                ($this->option('device') ? " matching ulid {$this->option('device')}." : '.'));

            return self::FAILURE;
        }

        $body = $this->option('message') ?: 'This is a test push from '.config('app.name').'.';

        $sent = 0;
        $failed = 0;

        foreach ($devices as $device) {
            $message = CloudMessage::new()
                ->withToken($device->device_token)
                ->withNotification(['title' => config('app.name').' — Test', 'body' => $body])
                ->withData(['type' => 'test']);

            if ($this->option('low-priority')) {
                $message = $message->withLowestPossiblePriority();
            }

            try {
                Firebase::messaging()->send($message);
                $sent++;
                $this->info("✓ {$device->name} ({$device->ulid})");
            } catch (Throwable $e) {
                $failed++;
                $detail = $e instanceof MessagingException
                    ? json_encode($e->errors())
                    : $e->getMessage();
                $this->error("✗ {$device->name} ({$device->ulid}): {$detail}");
            }
        }

        $this->newLine();
        $this->line("Sent: {$sent}  Failed: {$failed}");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
