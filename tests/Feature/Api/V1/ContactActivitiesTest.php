<?php

namespace Tests\Feature\Api\V1;

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
            ->assertJsonPath('data.0.user.name', $user->name)
            ->assertJsonPath('data.1.event', 'created')
            ->assertJsonPath('data.1.changes', [])
            ->assertJsonPath('meta.total', 2);

        $this->assertSame([
            ['field' => 'lastname', 'old' => 'Hergan', 'new' => 'Katzbauer', 'label' => 'Last name'],
            ['field' => 'birth_name', 'old' => null, 'new' => 'Hergan', 'label' => 'Birth name'],
        ], $response->json('data.0.changes'));
        $this->assertNotNull($response->json('data.0.created_at'));
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
