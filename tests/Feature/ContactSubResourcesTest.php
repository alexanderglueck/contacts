<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Contact;
use App\Models\ContactAddress;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactNote;
use App\Models\ContactNumber;
use App\Models\ContactUrl;
use App\Models\GiftIdea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * CRUD over the contact-detail slideovers. Each section has no own GET
 * (read happens via lazy props on the parent contact's show page), so
 * the C/U/D endpoints are what matter here.
 */
class ContactSubResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser();

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    // ── Phone numbers ────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_phone_number()
    {
        $contact = $this->aContact();

        $this->post(route('contact_numbers.store', $contact->ulid), [
            'name' => 'Mobile',
            'number' => '+43 660 1234567',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_numbers', [
            'contact_id' => $contact->id,
            'name' => 'Mobile',
            'number' => '+43 660 1234567',
        ]);
    }

    #[Test]
    public function it_can_update_a_phone_number()
    {
        $contact = $this->aContact();
        $number = $contact->numbers()->create([
            'name' => 'Mobile',
            'number' => '+43 660 1111111',
        ]);

        $this->put(route('contact_numbers.update', [$contact->ulid, $number->ulid]), [
            'name' => 'Work',
            'number' => '+43 660 2222222',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_numbers', ['id' => $number->id, 'name' => 'Work', 'number' => '+43 660 2222222']);
    }

    #[Test]
    public function it_can_delete_a_phone_number()
    {
        $contact = $this->aContact();
        $number = $contact->numbers()->create(['name' => 'X', 'number' => '+43 1 23']);

        $this->delete(route('contact_numbers.destroy', [$contact->ulid, $number->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_numbers', ['id' => $number->id]);
    }

    #[Test]
    public function it_rejects_a_phone_number_with_letters()
    {
        $contact = $this->aContact();

        $this->post(route('contact_numbers.store', $contact->ulid), [
            'name' => 'Mobile',
            'number' => 'NOT-A-NUMBER',
        ])->assertSessionHasErrors('number');

        $this->assertDatabaseMissing('contact_numbers', ['contact_id' => $contact->id]);
    }

    // ── Emails ────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_an_email()
    {
        $contact = $this->aContact();

        $this->post(route('contact_emails.store', $contact->ulid), [
            'name' => 'Work',
            'email' => 'jane@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_emails', [
            'contact_id' => $contact->id,
            'email' => 'jane@example.com',
        ]);
    }

    #[Test]
    public function it_can_update_an_email()
    {
        $contact = $this->aContact();
        $email = $contact->emails()->create(['name' => 'A', 'email' => 'a@example.com']);

        $this->put(route('contact_emails.update', [$contact->ulid, $email->ulid]), [
            'name' => 'B', 'email' => 'b@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_emails', ['id' => $email->id, 'email' => 'b@example.com']);
    }

    #[Test]
    public function it_can_delete_an_email()
    {
        $contact = $this->aContact();
        $email = $contact->emails()->create(['name' => 'A', 'email' => 'a@example.com']);

        $this->delete(route('contact_emails.destroy', [$contact->ulid, $email->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_emails', ['id' => $email->id]);
    }

    // ── URLs ──────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_url()
    {
        $contact = $this->aContact();

        $this->post(route('contact_urls.store', $contact->ulid), [
            'name' => 'Homepage',
            'url' => 'https://example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_urls', [
            'contact_id' => $contact->id,
            'url' => 'https://example.com',
        ]);
    }

    #[Test]
    public function it_can_update_a_url()
    {
        $contact = $this->aContact();
        $url = $contact->urls()->create(['name' => 'X', 'url' => 'https://x.test']);

        $this->put(route('contact_urls.update', [$contact->ulid, $url->ulid]), [
            'name' => 'Y', 'url' => 'https://y.test',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_urls', ['id' => $url->id, 'url' => 'https://y.test']);
    }

    #[Test]
    public function it_can_delete_a_url()
    {
        $contact = $this->aContact();
        $url = $contact->urls()->create(['name' => 'X', 'url' => 'https://x.test']);

        $this->delete(route('contact_urls.destroy', [$contact->ulid, $url->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_urls', ['id' => $url->id]);
    }

    // ── Notes ─────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_note()
    {
        $contact = $this->aContact();

        $this->post(route('contact_notes.store', $contact->ulid), [
            'name' => 'First meeting',
            'note' => 'Discussed the proposal.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_notes', [
            'contact_id' => $contact->id,
            'name' => 'First meeting',
        ]);
    }

    #[Test]
    public function it_can_update_a_note()
    {
        $contact = $this->aContact();
        $note = $contact->notes()->create(['name' => 'Old', 'note' => 'before']);

        $this->put(route('contact_notes.update', [$contact->ulid, $note->ulid]), [
            'name' => 'New', 'note' => 'after',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_notes', ['id' => $note->id, 'name' => 'New']);
    }

    #[Test]
    public function it_can_delete_a_note()
    {
        $contact = $this->aContact();
        $note = $contact->notes()->create(['name' => 'X', 'note' => 'y']);

        $this->delete(route('contact_notes.destroy', [$contact->ulid, $note->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_notes', ['id' => $note->id]);
    }

    // ── Calls ─────────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_call()
    {
        $contact = $this->aContact();

        $this->post(route('contact_calls.store', $contact->ulid), [
            'called_at' => '24.05.2026 14:30',
            'note' => 'Confirmed the appointment.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_calls', [
            'contact_id' => $contact->id,
            'note' => 'Confirmed the appointment.',
        ]);
    }

    #[Test]
    public function it_stores_a_call_submitted_as_utc_iso()
    {
        // The Vue client converts the datetime-local input to a UTC ISO-8601
        // string (e.g. 20:00 Vienna -> 18:00Z) before posting. The instant must
        // land in the DB as UTC, unshifted.
        $contact = $this->aContact();

        $this->post(route('contact_calls.store', $contact->ulid), [
            'called_at' => '2026-05-24T18:00:00.000Z',
            'note' => 'iso payload',
        ])->assertRedirect();

        $this->assertSame(
            '2026-05-24 18:00:00',
            ContactCall::where('note', 'iso payload')->first()->called_at->utc()->format('Y-m-d H:i:s')
        );
    }

    #[Test]
    public function it_can_update_a_call()
    {
        $contact = $this->aContact();
        $call = $contact->calls()->create([
            'note' => 'before',
            'called_at' => '2026-05-24T14:30:00',
        ]);

        $this->put(route('contact_calls.update', [$contact->ulid, $call->ulid]), [
            'called_at' => '24.05.2026 15:00',
            'note' => 'after',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_calls', ['id' => $call->id, 'note' => 'after']);
    }

    #[Test]
    public function it_can_delete_a_call()
    {
        $contact = $this->aContact();
        $call = $contact->calls()->create([
            'note' => 'x',
            'called_at' => '2026-05-24T14:30:00',
        ]);

        $this->delete(route('contact_calls.destroy', [$contact->ulid, $call->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_calls', ['id' => $call->id]);
    }

    // ── Important dates ──────────────────────────────────────────────────

    #[Test]
    public function it_can_store_an_important_date_with_year()
    {
        $contact = $this->aContact();

        $this->post(route('contact_dates.store', $contact->ulid), [
            'name' => 'Anniversary',
            // The Vue form uses <input type="date">, which submits Y-m-d.
            'date' => '2020-05-24',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_dates', [
            'contact_id' => $contact->id,
            'name' => 'Anniversary',
        ]);
    }

    #[Test]
    public function it_can_store_a_date_without_year_when_skip_year_is_set()
    {
        $contact = $this->aContact();

        $this->post(route('contact_dates.store', $contact->ulid), [
            'name' => 'Onomastico',
            // Dates are always stored as Y-m-d; skip_year only hides the year
            // when the date is displayed (see ContactDate::getFormattedDate).
            'date' => '2020-05-24',
            'skip_year' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_dates', [
            'contact_id' => $contact->id,
            'name' => 'Onomastico',
            'skip_year' => 1,
        ]);
    }

    #[Test]
    public function it_can_delete_an_important_date()
    {
        $contact = $this->aContact();
        $date = $contact->dates()->create([
            'name' => 'X',
            'date' => '2020-05-24',
            'skip_year' => 0,
        ]);

        $this->delete(route('contact_dates.destroy', [$contact->ulid, $date->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_dates', ['id' => $date->id]);
    }

    // ── Gift ideas ────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_gift_idea()
    {
        $contact = $this->aContact();

        $this->post(route('gift_ideas.store', $contact->ulid), [
            'name' => 'Bike helmet',
            'description' => 'They mentioned the old one cracked.',
            'url' => 'https://example.com/helmet',
            'due_at' => '24.12.2026',
        ])->assertRedirect();

        $this->assertDatabaseHas('gift_ideas', [
            'contact_id' => $contact->id,
            'name' => 'Bike helmet',
        ]);
    }

    #[Test]
    public function it_can_update_a_gift_idea()
    {
        $contact = $this->aContact();
        $gift = GiftIdea::create(['contact_id' => $contact->id, 'name' => 'Old']);

        $this->put(route('gift_ideas.update', [$contact->ulid, $gift->ulid]), [
            'name' => 'New',
        ])->assertRedirect();

        $this->assertDatabaseHas('gift_ideas', ['id' => $gift->id, 'name' => 'New']);
    }

    #[Test]
    public function it_can_delete_a_gift_idea()
    {
        $contact = $this->aContact();
        $gift = GiftIdea::create(['contact_id' => $contact->id, 'name' => 'X']);

        $this->delete(route('gift_ideas.destroy', [$contact->ulid, $gift->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('gift_ideas', ['id' => $gift->id]);
    }

    // ── Addresses ────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_an_address()
    {
        $contact = $this->aContact();
        $countryId = DB::table('countries')->value('id');

        $this->post(route('contact_addresses.store', $contact->ulid), [
            'name' => 'Home',
            'street' => 'Hauptstraße 1',
            'zip' => '8010',
            'city' => 'Graz',
            'state' => 'Steiermark',
            'country_id' => $countryId,
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_addresses', [
            'contact_id' => $contact->id,
            'street' => 'Hauptstraße 1',
            'city' => 'Graz',
        ]);
    }

    #[Test]
    public function it_can_delete_an_address()
    {
        $contact = $this->aContact();
        $countryId = DB::table('countries')->value('id');
        $address = ContactAddress::create([
            'contact_id' => $contact->id,
            'name' => 'Home',
            'street' => 'X 1',
            'zip' => '1010',
            'city' => 'Vienna',
            'state' => 'Wien',
            'country_id' => $countryId,
        ]);

        $this->delete(route('contact_addresses.destroy', [$contact->ulid, $address->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('contact_addresses', ['id' => $address->id]);
    }

    // ── Comments ─────────────────────────────────────────────────────────

    #[Test]
    public function it_can_store_a_comment()
    {
        $contact = $this->aContact();

        $this->post(route('comments.store', $contact->ulid), [
            'comment' => 'First impression: friendly.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'contact_id' => $contact->id,
            'comment' => 'First impression: friendly.',
        ]);
    }

    #[Test]
    public function deleting_a_comment_with_replies_tombstones_it_instead_of_removing_the_row()
    {
        $contact = $this->aContact();

        $this->post(route('comments.store', $contact->ulid), ['comment' => 'Parent']);
        $parent = Comment::where('contact_id', $contact->id)->latest('id')->first();

        $this->post(route('comments.store', $contact->ulid), [
            'comment' => 'Reply',
            'parent_ulid' => $parent->ulid,
        ]);

        $this->delete(route('comments.destroy', [$contact->ulid, $parent->ulid]))->assertRedirect();

        // Row still exists, but its body is null (tombstoned) so the
        // reply has something to anchor to in the UI.
        $this->assertDatabaseHas('comments', ['id' => $parent->id, 'comment' => null]);
    }

    #[Test]
    public function deleting_a_childless_comment_removes_the_row()
    {
        $contact = $this->aContact();

        $this->post(route('comments.store', $contact->ulid), ['comment' => 'Standalone']);
        $comment = Comment::where('contact_id', $contact->id)->latest('id')->first();

        $this->delete(route('comments.destroy', [$contact->ulid, $comment->ulid]))->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
