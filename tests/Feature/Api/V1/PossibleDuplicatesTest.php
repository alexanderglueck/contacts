<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * GET /api/v1/contacts/possible-duplicates and the group ids in the contact
 * list, both used by the Android client.
 */
class PossibleDuplicatesTest extends TestCase
{
    use RefreshDatabase;

    private function makeContact(int $userId, array $attributes = []): Contact
    {
        return create(Contact::class, array_merge([
            'created_by' => $userId,
            'updated_by' => $userId,
        ], $attributes));
    }

    #[Test]
    public function it_matches_the_last_name_and_the_birth_name_case_insensitively()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);
        $cornelia = $this->makeContact($user->id, ['firstname' => 'Cornelia', 'lastname' => 'Katzbauer', 'birth_name' => 'Hergan']);
        $this->makeContact($user->id, ['firstname' => 'Cornelia', 'lastname' => 'Huber']);
        $this->makeContact($user->id, ['firstname' => 'Maria', 'lastname' => 'Katzbauer']);

        foreach (['Katzbauer', 'hergan'] as $lastname) {
            $this->getJson(route('api.v1.contacts.possible_duplicates', ['firstname' => 'cornelia', 'lastname' => $lastname]))
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.ulid', $cornelia->ulid);
        }

        // A new contact entered under the old name, with the new one as birth name.
        $this->getJson(route('api.v1.contacts.possible_duplicates', [
            'firstname' => 'Cornelia', 'lastname' => 'Schmidt', 'birth_name' => 'Katzbauer',
        ]))->assertOk()->assertJsonCount(1, 'data');

        $this->getJson(route('api.v1.contacts.possible_duplicates', [
            'firstname' => 'Cornelia', 'lastname' => 'Katzbauer', 'exclude' => $cornelia->ulid,
        ]))->assertOk()->assertJsonCount(0, 'data');
    }

    #[Test]
    public function first_names_match_by_their_parts()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);
        $cornelia = $this->makeContact($user->id, ['firstname' => 'Cornelia Maria', 'lastname' => 'Katzbauer', 'birth_name' => 'Hergan']);
        $this->makeContact($user->id, ['firstname' => 'Anna', 'lastname' => 'Hergan']);

        foreach (['Cornelia', 'maria', 'Cornelia-Maria'] as $firstname) {
            $this->getJson(route('api.v1.contacts.possible_duplicates', ['firstname' => $firstname, 'lastname' => 'Hergan']))
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.ulid', $cornelia->ulid);
        }

        $this->getJson(route('api.v1.contacts.possible_duplicates', ['firstname' => 'Corinna', 'lastname' => 'Katzbauer']))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_does_not_match_contacts_of_another_team()
    {
        $alice = $this->createUser();
        $this->makeContact($alice->id, ['firstname' => 'Cornelia', 'lastname' => 'Katzbauer']);

        $bob = $this->createUser();
        Sanctum::actingAs($bob);

        $this->getJson(route('api.v1.contacts.possible_duplicates', ['firstname' => 'Cornelia', 'lastname' => 'Katzbauer']))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_requires_first_and_last_name()
    {
        Sanctum::actingAs($this->createUser());

        $this->getJson(route('api.v1.contacts.possible_duplicates'))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['firstname', 'lastname']);
    }

    #[Test]
    public function the_contact_list_includes_group_ids()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);
        $contact = $this->makeContact($user->id);
        $group = ContactGroup::factory()->create(['team_id' => $user->current_team_id, 'created_by' => $user->id, 'updated_by' => $user->id]);
        $contact->contactGroups()->attach($group->id);

        $this->getJson(route('api.v1.contacts.index'))
            ->assertOk()
            ->assertJsonPath('data.0.group_ids', [$group->id]);
    }
}
