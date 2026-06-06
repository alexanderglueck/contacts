<?php

namespace Tests\Feature;

use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\RuntimeException as FirebaseRuntimeException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * push:test — manual FCM verification command.
 */
class SendTestPushTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_to_each_device_with_a_token_and_succeeds()
    {
        $user = $this->createUser();
        create(Device::class, ['user_id' => $user->id, 'device_token' => 'tok-1']);
        create(Device::class, ['user_id' => $user->id, 'device_token' => 'tok-2']);
        create(Device::class, ['user_id' => $user->id, 'device_token' => null]); // skipped

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')->twice();
        Firebase::shouldReceive('messaging')->andReturn($messaging);

        $this->artisan('push:test', ['user' => $user->email])->assertSuccessful();
    }

    #[Test]
    public function it_fails_when_the_user_has_no_push_devices()
    {
        $user = $this->createUser();

        $this->artisan('push:test', ['user' => $user->email])->assertFailed();
    }

    #[Test]
    public function it_reports_failure_when_firebase_throws()
    {
        $user = $this->createUser();
        create(Device::class, ['user_id' => $user->id, 'device_token' => 'tok-1']);

        $messaging = Mockery::mock(Messaging::class);
        $messaging->shouldReceive('send')->once()->andThrow(new FirebaseRuntimeException('bad token'));
        Firebase::shouldReceive('messaging')->andReturn($messaging);

        $this->artisan('push:test', ['user' => $user->email])->assertFailed();
    }

    #[Test]
    public function it_fails_for_an_unknown_user()
    {
        $this->artisan('push:test', ['user' => 'nobody@example.com'])->assertFailed();
    }
}
