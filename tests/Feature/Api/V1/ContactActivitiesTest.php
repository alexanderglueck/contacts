<?php

namespace Tests\Feature\Api\V1;

use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * GET /api/v1/contacts/{contact}/activities — the edit history the Android
 * client shows behind the contact's overflow menu.
 */
class ContactActivitiesTest extends TestCase
{
    use RefreshDatabase;

    private function makeContact(int $userId, array $attributes = []): Contact
    {
        return create(Contact::class, array_merge([
            'created_by' => $userId,
            'updated_by' => $userId,
            'firstname' => 'Cornelia',
            'lastname' => 'Hergan',
        ], $attributes));
    }

    #[Test]
    public function it_lists_the_history_newest_first_with_the_changed_fields()
    {
        $user = $this->createUser();
        $contact = $this->makeContact($user->id);
        $contact->update(['lastname' => 'Katzbauer', 'birth_name' => 'Hergan']);

        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.contacts.activities.index', $contact->ulid));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.event', 'updated')
            ->assertJsonPath('data.0.subject', 'contact')
            ->assertJsonPath('data.0.summary', 'Contact updated')
            ->assertJsonPath('data.0.user.name', $user->name)
            ->assertJsonPath('data.1.event', 'created')
            ->assertJsonPath('data.1.summary', 'Contact created')
            ->assertJsonPath('meta.total', 2);

        $this->assertSame([
            ['field' => 'lastname', 'label' => 'Last name', 'old' => 'Hergan', 'new' => 'Katzbauer'],
            ['field' => 'birth_name', 'label' => 'Birth name', 'old' => null, 'new' => 'Hergan'],
        ], $response->json('data.0.changes'));
        $this->assertNotNull($response->json('data.0.created_at'));

        // A created entry lists the values the contact started with.
        $created = collect($response->json('data.1.changes'))->keyBy('field');
        $this->assertSame([null, 'Cornelia'], [$created['firstname']['old'], $created['firstname']['new']]);
        $this->assertSame('Yes', $created['active']['new']);
    }

    #[Test]
    public function numbers_emails_and_addresses_show_up_in_the_contacts_history()
    {
        $user = $this->createUser();
        $contact = $this->makeContact($user->id);
        Sanctum::actingAs($user);

        $number = $this->postJson(route('api.v1.contacts.numbers.store', $contact->ulid), [
            'name' => 'Mobil',
            'number' => '+43 664 1234567',
        ])->assertCreated()->json('data.ulid');

        $this->patchJson(route('api.v1.contacts.numbers.update', [$contact->ulid, $number]), [
            'name' => 'Arbeit',
            'number' => '+43 664 1234567',
        ])->assertOk();

        $this->postJson(route('api.v1.contacts.emails.store', $contact->ulid), [
            'name' => 'Privat',
            'email' => 'cornelia@example.com',
        ])->assertCreated();

        $this->deleteJson(route('api.v1.contacts.numbers.destroy', [$contact->ulid, $number]))->assertNoContent();

        $data = $this->getJson(route('api.v1.contacts.activities.index', $contact->ulid))
            ->assertOk()
            ->json('data');

        $this->assertSame(
            ['Phone number deleted', 'Email address added', 'Phone number updated', 'Phone number added', 'Contact created'],
            array_column($data, 'summary'),
        );
        $this->assertSame(
            [['field' => 'name', 'label' => 'Label', 'old' => 'Mobil', 'new' => 'Arbeit']],
            $data[2]['changes'],
        );
        $this->assertSame('Mobil', collect($data[3]['changes'])->firstWhere('field', 'name')['new']);
        $this->assertSame('Arbeit', collect($data[0]['changes'])->firstWhere('field', 'name')['old']);
    }

    #[Test]
    public function touching_the_contact_or_geocoding_records_nothing_extra()
    {
        $user = $this->createUser();
        $contact = $this->makeContact($user->id);
        Sanctum::actingAs($user);

        // Adding a number touches the contact's updated_at; that is no edit.
        $this->postJson(route('api.v1.contacts.numbers.store', $contact->ulid), [
            'name' => 'Mobil',
            'number' => '+43 664 1234567',
        ])->assertCreated();

        $contact->refresh()->touch();

        $this->assertSame(
            ['created_contactnumber', 'created_contact'],
            Activity::where('contact_id', $contact->id)->latest('id')->pluck('action')->all(),
        );
    }

    #[Test]
    public function labels_follow_the_users_locale()
    {
        $user = $this->createUser();
        $user->forceFill(['locale' => 'de'])->save();
        $contact = $this->makeContact($user->id);
        $contact->update(['lastname' => 'Katzbauer']);

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.contacts.activities.index', $contact->ulid))
            ->assertOk()
            ->assertJsonPath('data.0.summary', 'Kontakt geändert')
            ->assertJsonPath('data.0.changes.0.label', 'Nachname');
    }

    #[Test]
    public function it_paginates()
    {
        $user = $this->createUser();
        $contact = $this->makeContact($user->id);
        foreach (['A', 'B', 'C'] as $name) {
            $contact->update(['nickname' => $name]);
        }

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.contacts.activities.index', [$contact->ulid, 'per_page' => 2, 'page' => 2]))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('data.1.event', 'created');
    }

    #[Test]
    public function it_returns_404_for_a_contact_of_another_tenant()
    {
        $alice = $this->createUser();
        $alicesContact = $this->makeContact($alice->id);

        $bob = $this->createUser();
        Sanctum::actingAs($bob);

        $this->getJson(route('api.v1.contacts.activities.index', $alicesContact->ulid))
            ->assertStatus(404);
    }
}
