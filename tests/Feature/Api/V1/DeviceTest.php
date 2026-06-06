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
    public function registering_a_device_requires_a_name_and_token()
    {
        Sanctum::actingAs($this->createUser());

        $this->postJson(route('api.v1.devices.store'), [])->assertStatus(422);
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
