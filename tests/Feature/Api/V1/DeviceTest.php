<?php

namespace Tests\Feature\Api\V1;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/devices — server-side push device registration.
 */
class DeviceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function device_endpoints_require_authentication()
    {
        $this->getJson(route('api.v1.devices.index'))->assertStatus(401);
        $this->postJson(route('api.v1.devices.store'))->assertStatus(401);
    }

    #[Test]
    public function a_user_can_register_a_device_with_a_token()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.devices.store'), [
            'name' => 'My iPhone',
            'token' => 'fcm-token-123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'My iPhone')
            ->assertJsonPath('data.can_push', true);

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'name' => 'My iPhone',
            'device_token' => 'fcm-token-123',
        ]);
    }

    #[Test]
    public function re_registering_the_same_token_updates_the_existing_row_instead_of_duplicating()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Old name',
            'token' => 'same-token',
        ])->assertCreated();

        // Same token (e.g. app data cleared) → update, not a second row.
        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'New name',
            'token' => 'same-token',
        ])->assertOk();

        $this->assertSame(1, $user->devices()->count());
        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'device_token' => 'same-token',
            'name' => 'New name',
        ]);
    }

    #[Test]
    public function registering_a_device_requires_a_name_and_an_identifier()
    {
        Sanctum::actingAs($this->createUser());

        $this->postJson(route('api.v1.devices.store'), [])->assertStatus(422);

        // A name alone is not enough — we need something to address the device with.
        $this->postJson(route('api.v1.devices.store'), ['name' => 'Nameless'])
            ->assertStatus(422);
    }

    #[Test]
    public function a_user_can_register_a_device_with_a_fid_and_token()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Google Pixel 9',
            'token' => 'fcm-token-123',
            'fid' => 'installation-id-abc',
        ])->assertCreated()->assertJsonPath('data.can_push', true);

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'device_token' => 'fcm-token-123',
            'fid' => 'installation-id-abc',
        ]);
    }

    #[Test]
    public function a_device_can_register_with_a_fid_alone()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        // Phase 2: the app drops the deprecated token entirely.
        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Pixel',
            'fid' => 'fid-only',
        ])->assertCreated()->assertJsonPath('data.can_push', true);

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'fid' => 'fid-only',
            'device_token' => null,
        ]);
    }

    #[Test]
    public function an_existing_token_only_device_adopts_the_fid_instead_of_duplicating()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        // Registered by an older build: token only, no FID.
        $existing = create(Device::class, [
            'user_id' => $user->id,
            'name' => 'My Pixel',
            'device_token' => 'legacy-token',
            'fid' => null,
        ]);

        // First check-in after the app update sends both. A fid-first-only
        // lookup would miss and insert a second row, leaving the original to
        // be pushed to as well.
        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'My Pixel',
            'token' => 'legacy-token',
            'fid' => 'new-installation-id',
        ])->assertOk();

        $this->assertSame(1, $user->devices()->count());
        $this->assertSame('new-installation-id', $existing->fresh()->fid);
    }

    #[Test]
    public function a_rotated_token_updates_the_row_matched_by_fid()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $device = create(Device::class, [
            'user_id' => $user->id,
            'device_token' => 'old-token',
            'fid' => 'stable-fid',
        ]);

        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Pixel',
            'token' => 'rotated-token',
            'fid' => 'stable-fid',
        ])->assertOk();

        $this->assertSame(1, $user->devices()->count());
        $this->assertSame('rotated-token', $device->fresh()->device_token);
    }

    #[Test]
    public function registering_with_a_fid_alone_keeps_the_stored_token_as_a_fallback()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $device = create(Device::class, [
            'user_id' => $user->id,
            'device_token' => 'keep-me',
            'fid' => 'stable-fid',
        ]);

        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Pixel',
            'fid' => 'stable-fid',
        ])->assertOk();

        $this->assertSame('keep-me', $device->fresh()->device_token);
    }

    #[Test]
    public function a_device_belonging_to_another_user_is_never_matched()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        // Same physical device, previously registered to a different account.
        create(Device::class, ['user_id' => create(User::class)->id, 'fid' => 'shared-fid']);

        $this->postJson(route('api.v1.devices.store'), [
            'name' => 'Shared phone',
            'fid' => 'shared-fid',
        ])->assertCreated();

        $this->assertSame(1, $user->devices()->count());
    }

    #[Test]
    public function index_lists_only_the_callers_devices()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        create(Device::class, ['user_id' => $user->id, 'name' => 'Mine']);
        create(Device::class, ['user_id' => create(User::class)->id, 'name' => 'Theirs']);

        $response = $this->getJson(route('api.v1.devices.index'));

        $response->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mine');
    }

    #[Test]
    public function a_user_can_delete_their_own_device_but_not_anothers()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $mine = create(Device::class, ['user_id' => $user->id]);
        $theirs = create(Device::class, ['user_id' => create(User::class)->id]);

        $this->deleteJson(route('api.v1.devices.destroy', $mine))->assertNoContent();
        $this->assertDatabaseMissing('devices', ['id' => $mine->id]);

        $this->deleteJson(route('api.v1.devices.destroy', $theirs))->assertStatus(403);
        $this->assertDatabaseHas('devices', ['id' => $theirs->id]);
    }
}
