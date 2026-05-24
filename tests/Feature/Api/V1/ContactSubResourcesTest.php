<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/contacts/{contact}/{section} CRUD. The route binding is scoped
 * (->scopeBindings() in routes/api.php) so the framework guarantees the
 * nested resource actually belongs to the parent contact — the leak-guard
 * tests below verify that.
 */
class ContactSubResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    // ── Phone numbers ────────────────────────────────────────────────────

    #[Test]
    public function it_indexes_phone_numbers()
    {
        $contact = $this->aContact();
        $contact->numbers()->create(['name' => 'Mobile', 'number' => '+436601234567']);
        $contact->numbers()->create(['name' => 'Work', 'number' => '+436601234568']);

        $response = $this->getJson(route('api.v1.contacts.numbers.index', $contact->ulid));

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    #[Test]
    public function it_stores_a_phone_number()
    {
        $contact = $this->aContact();

        $response = $this->postJson(route('api.v1.contacts.numbers.store', $contact->ulid), [
            'name' => 'Mobile',
            'number' => '+436601234567',
        ]);

        $response->assertCreated();
        $this->assertSame('Mobile', $response->json('data.name'));
        $this->assertDatabaseHas('contact_numbers', ['contact_id' => $contact->id, 'name' => 'Mobile']);
    }

    #[Test]
    public function it_updates_a_phone_number()
    {
        $contact = $this->aContact();
        $number = $contact->numbers()->create(['name' => 'Old', 'number' => '+436601111111']);

        $this->patchJson(route('api.v1.contacts.numbers.update', [$contact->ulid, $number->ulid]), [
            'name' => 'New',
            'number' => '+436602222222',
        ])->assertOk();

        $this->assertDatabaseHas('contact_numbers', ['id' => $number->id, 'name' => 'New']);
    }

    #[Test]
    public function it_deletes_a_phone_number()
    {
        $contact = $this->aContact();
        $number = $contact->numbers()->create(['name' => 'X', 'number' => '+436601234567']);

        $this->deleteJson(route('api.v1.contacts.numbers.destroy', [$contact->ulid, $number->ulid]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_numbers', ['id' => $number->id]);
    }

    #[Test]
    public function nested_route_binding_404s_when_the_number_belongs_to_a_different_contact()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $own = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $other = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $otherNumber = $other->numbers()->create(['name' => 'Other', 'number' => '+436605555555']);

        // Asking for $own/numbers/{otherNumber-ulid} must 404 — scope bindings
        // enforce the parent-child relationship at the framework level.
        $this->getJson(route('api.v1.contacts.numbers.show', [$own->ulid, $otherNumber->ulid]))
            ->assertStatus(404);
    }

    // ── Emails ────────────────────────────────────────────────────────────

    #[Test]
    public function it_indexes_and_stores_emails()
    {
        $contact = $this->aContact();
        $contact->emails()->create(['name' => 'Personal', 'email' => 'a@example.com']);

        $this->getJson(route('api.v1.contacts.emails.index', $contact->ulid))
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->postJson(route('api.v1.contacts.emails.store', $contact->ulid), [
            'name' => 'Work',
            'email' => 'b@example.com',
        ])->assertCreated();

        $this->assertDatabaseHas('contact_emails', ['email' => 'b@example.com']);
    }

    #[Test]
    public function it_deletes_an_email()
    {
        $contact = $this->aContact();
        $email = $contact->emails()->create(['name' => 'X', 'email' => 'x@example.com']);

        $this->deleteJson(route('api.v1.contacts.emails.destroy', [$contact->ulid, $email->ulid]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_emails', ['id' => $email->id]);
    }

    // ── URLs ──────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_a_url()
    {
        $contact = $this->aContact();

        $stored = $this->postJson(route('api.v1.contacts.urls.store', $contact->ulid), [
            'name' => 'Homepage',
            'url' => 'https://example.com',
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.urls.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_urls', ['ulid' => $stored]);
    }

    // ── Notes ─────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_a_note()
    {
        $contact = $this->aContact();

        $stored = $this->postJson(route('api.v1.contacts.notes.store', $contact->ulid), [
            'name' => 'Intro',
            'note' => 'Met at the conference.',
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.notes.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_notes', ['ulid' => $stored]);
    }

    // ── Calls ─────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_a_call()
    {
        $contact = $this->aContact();

        $stored = $this->postJson(route('api.v1.contacts.calls.store', $contact->ulid), [
            'called_at' => '2026-05-24T14:30:00',
            'note' => 'Confirmed the appointment.',
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.calls.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_calls', ['ulid' => $stored]);
    }

    // ── Important dates ──────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_an_important_date()
    {
        $contact = $this->aContact();

        $stored = $this->postJson(route('api.v1.contacts.dates.store', $contact->ulid), [
            'name' => 'Anniversary',
            'date' => '2020-05-24',
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.dates.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_dates', ['ulid' => $stored]);
    }

    // ── Gift ideas ────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_a_gift_idea()
    {
        $contact = $this->aContact();

        $stored = $this->postJson(route('api.v1.contacts.gift-ideas.store', $contact->ulid), [
            'name' => 'Bike helmet',
            'description' => 'The old one cracked.',
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.gift-ideas.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('gift_ideas', ['ulid' => $stored]);
    }

    // ── Addresses ────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_and_delete_an_address()
    {
        $contact = $this->aContact();
        $countryId = DB::table('countries')->value('id');

        $stored = $this->postJson(route('api.v1.contacts.addresses.store', $contact->ulid), [
            'name' => 'Home',
            'street' => 'Hauptstraße 1',
            'zip' => '8010',
            'city' => 'Graz',
            'state' => 'Steiermark',
            'country_id' => $countryId,
        ])->assertCreated()->json('data.ulid');

        $this->deleteJson(route('api.v1.contacts.addresses.destroy', [$contact->ulid, $stored]))
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_addresses', ['ulid' => $stored]);
    }
}
