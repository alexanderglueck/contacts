<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/contacts CRUD. Sanctum-authenticated, tenant-scoped via the
 * BelongsToTenantScope on Contact (driven by the SetTenant middleware).
 */
class ContactsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_calls_are_rejected_with_401()
    {
        $this->getJson(route('api.v1.contacts.index'))->assertStatus(401);
    }

    #[Test]
    public function index_paginates_the_current_tenants_contacts()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        for ($i = 0; $i < 3; $i++) {
            create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        }

        $response = $this->getJson(route('api.v1.contacts.index'));

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
        $this->assertSame(3, $response->json('meta.total'));
    }

    #[Test]
    public function index_does_not_leak_contacts_from_another_tenant()
    {
        $alice = $this->createUser();
        create(Contact::class, ['created_by' => $alice->id, 'updated_by' => $alice->id]);

        $bob = $this->createUser();
        Sanctum::actingAs($bob);

        $response = $this->getJson(route('api.v1.contacts.index'));

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function show_returns_the_full_detail_payload()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $contact = create(Contact::class, [
            'firstname' => 'Jane',
            'lastname' => 'Bond',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.contacts.show', $contact->ulid));

        $response->assertOk();
        $this->assertSame($contact->ulid, $response->json('data.ulid'));
        $this->assertSame('Jane', $response->json('data.firstname'));
        // Sub-resource keys are part of the contract — clients rely on them
        // being arrays even when empty.
        foreach (['numbers', 'emails', 'urls', 'notes', 'dates', 'calls', 'gift_ideas', 'addresses'] as $key) {
            $this->assertIsArray($response->json("data.{$key}"));
        }
    }

    #[Test]
    public function show_returns_404_for_a_contact_belonging_to_another_tenant()
    {
        $alice = $this->createUser();
        $alicesContact = create(Contact::class, [
            'created_by' => $alice->id,
            'updated_by' => $alice->id,
        ]);

        $bob = $this->createUser();
        Sanctum::actingAs($bob);

        $this->getJson(route('api.v1.contacts.show', $alicesContact->ulid))
            ->assertStatus(404);
    }

    #[Test]
    public function store_creates_a_contact_with_iso_dates()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.contacts.store'), [
            'salutation' => 'Frau',
            'firstname' => 'Maria',
            'lastname' => 'Rossi',
            'gender_id' => 2,
            'date_of_birth' => '1990-05-24',
        ]);

        $response->assertCreated();
        $this->assertSame('Maria', $response->json('data.firstname'));
        $this->assertDatabaseHas('contacts', ['firstname' => 'Maria', 'lastname' => 'Rossi']);
    }

    #[Test]
    public function store_returns_422_with_field_scoped_errors_for_missing_required_fields()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.contacts.store'), []);

        $response->assertStatus(422);
        $this->assertArrayHasKey('salutation', $response->json('errors'));
        $this->assertArrayHasKey('firstname', $response->json('errors'));
        $this->assertArrayHasKey('lastname', $response->json('errors'));
    }

    #[Test]
    public function update_partial_only_touches_the_supplied_fields()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $contact = create(Contact::class, [
            'firstname' => 'Old',
            'lastname' => 'Name',
            'company' => 'Acme',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->patchJson(route('api.v1.contacts.update', $contact->ulid), [
            'firstname' => 'New',
        ])->assertOk();

        $fresh = $contact->fresh();
        $this->assertSame('New', $fresh->firstname);
        $this->assertSame('Name', $fresh->lastname);
        $this->assertSame('Acme', $fresh->company);
    }

    #[Test]
    public function destroy_removes_the_contact_and_returns_204()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $contact = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);

        $this->deleteJson(route('api.v1.contacts.destroy', $contact->ulid))
            ->assertNoContent();

        $this->assertModelMissing($contact);
    }

    #[Test]
    public function by_number_finds_a_contact_via_exact_e164_match()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $contact = create(Contact::class, [
            'firstname' => 'Sandra',
            'lastname' => 'Phone',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $contact->numbers()->create(['name' => 'Mobile', 'number' => '+436601234567']);

        $response = $this->getJson(route('api.v1.contacts.by_number', ['number' => '+436601234567']));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($contact->ulid, $response->json('data.0.contact_ulid'));
    }

    #[Test]
    public function by_number_returns_an_empty_array_when_nothing_matches()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.contacts.by_number', ['number' => '+10000000000']));

        $response->assertOk();
        $this->assertSame([], $response->json('data'));
    }
}
