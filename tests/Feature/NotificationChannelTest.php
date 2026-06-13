<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Device;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\TodaysDates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\RuntimeException as FirebaseRuntimeException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Notification settings gate mail and push independently, and the FcmChannel
 * sends low-priority pushes while swallowing per-device failures.
 */
class NotificationChannelTest extends TestCase
{
    use RefreshDatabase;

    private function settingsFor(User $user, array $flags): void
    {
        // user_id isn't mass-assignable, so set it explicitly.
        $setting = new NotificationSetting();
        $setting->fill($flags);
        $setting->user_id = $user->id;
        $setting->save();
    }

    #[Test]
    public function via_returns_only_mail_when_only_daily_mail_is_enabled()
    {
        $user = $this->createUser();
        $this->settingsFor($user, ['send_daily' => true]);

        $channels = (new TodaysDates())->via($user->fresh());

        $this->assertContains('mail', $channels);
        $this->assertNotContains(FcmChannel::class, $channels);
    }

    #[Test]
    public function via_returns_only_push_when_only_daily_push_is_enabled()
    {
        $user = $this->createUser();
        $this->settingsFor($user, ['send_daily_push' => true]);

        $channels = (new TodaysDates())->via($user->fresh());

        $this->assertContains(FcmChannel::class, $channels);
        $this->assertNotContains('mail', $channels);
    }

    #[Test]
    public function via_returns_nothing_when_both_daily_toggles_are_off()
    {
        $user = $this->createUser();
        $this->settingsFor($user, []);

        $this->assertSame([], (new TodaysDates())->via($user->fresh()));
    }

    #[Test]
    public function fcm_channel_sends_a_high_priority_data_only_message_per_device_token()
    {
        $user = $this->createUser();
        create(Device::class, ['user_id' => $user->id, 'device_token' => 'token-abc']);

        $captured = null;
        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')->once()->with(Mockery::on(function ($message) use (&$captured) {
            $captured = $message;

            return $message instanceof CloudMessage;
        }));
        Firebase::shouldReceive('messaging')->once()->andReturn($messaging);

        (new FcmChannel())->send($user->fresh(), new TodaysDates());

        $payload = $captured->jsonSerialize();
        // withHighestPossiblePriority() sets Android high priority + APNS 10 (Doze exemption).
        $this->assertSame('high', $payload['android']['priority'] ?? null);
        $this->assertSame('10', $payload['apns']['headers']['apns-priority'] ?? null);
        // Data-only: no notification block; title/body/type ride along in the data payload
        // so onMessageReceived runs in every app state and can deep-link.
        $this->assertArrayNotHasKey('notification', $payload);
        $this->assertSame('daily', $payload['data']['type'] ?? null);
        $this->assertArrayHasKey('title', $payload['data'] ?? []);
        $this->assertArrayHasKey('body', $payload['data'] ?? []);
    }

    #[Test]
    public function fcm_channel_swallows_delivery_failures()
    {
        $user = $this->createUser();
        create(Device::class, ['user_id' => $user->id, 'device_token' => 'token-abc']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')->once()->andThrow(
            new FirebaseRuntimeException('boom')
        );
        Firebase::shouldReceive('messaging')->once()->andReturn($messaging);

        // Should not throw.
        (new FcmChannel())->send($user->fresh(), new TodaysDates());

        $this->assertTrue(true);
    }

    #[Test]
    public function fcm_channel_does_nothing_when_the_user_has_no_device_tokens()
    {
        $user = $this->createUser();
        // device without a token must be ignored
        create(Device::class, ['user_id' => $user->id, 'device_token' => null]);

        Firebase::shouldReceive('messaging')->never();

        (new FcmChannel())->send($user->fresh(), new TodaysDates());

        $this->assertTrue(true);
    }
}
