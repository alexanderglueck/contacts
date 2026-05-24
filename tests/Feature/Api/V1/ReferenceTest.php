<?php

namespace Tests\Feature\Api\V1;

use App\Models\ContactGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/reference/* — read-only lookup tables for the mobile client.
 */
class ReferenceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function genders_returns_the_seeded_options()
    {
        Sanctum::actingAs($this->createUser());

        $response = $this->getJson(route('api.v1.reference.genders'));

        $response->assertOk();
        // Seeder inserts at least Male / Female.
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
        $this->assertArrayHasKey('id', $response->json('data.0'));
        $this->assertArrayHasKey('name', $response->json('data.0'));
    }

    #[Test]
    public function countries_returns_the_seeded_list()
    {
        Sanctum::actingAs($this->createUser());

        $response = $this->getJson(route('api.v1.reference.countries'));

        $response->assertOk();
        // The seeder populates hundreds of countries — anything north of 50
        // is plenty of confirmation that the lookup table came through.
        $this->assertGreaterThan(50, count($response->json('data')));
    }

    #[Test]
    public function contact_groups_returns_the_existing_groups()
    {
        $user = $this->createUser();
        create(ContactGroup::class, ['name' => 'Friends']);
        create(ContactGroup::class, ['name' => 'Colleagues']);
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.reference.contact_groups'));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Friends', $names);
        $this->assertContains('Colleagues', $names);
        // Note: the contact_groups table has no team_id column, so this
        // endpoint is NOT tenant-scoped in practice despite the controller
        // comment claiming otherwise — a known gap, separate from this
        // test's contract.
    }

    #[Test]
    public function reference_endpoints_require_authentication()
    {
        $this->getJson(route('api.v1.reference.genders'))->assertStatus(401);
        $this->getJson(route('api.v1.reference.countries'))->assertStatus(401);
        $this->getJson(route('api.v1.reference.contact_groups'))->assertStatus(401);
    }
}
