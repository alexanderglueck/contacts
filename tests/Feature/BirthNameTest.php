<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * birth_name keeps a contact findable under the surname they had before a
 * name change, e.g. Cornelia Katzbauer, née Hergan.
 */
class BirthNameTest extends TestCase
{
    use RefreshDatabase;

    private function makeContact(int $userId, array $attributes = []): Contact
    {
        return create(Contact::class, array_merge([
            'created_by' => $userId,
            'updated_by' => $userId,
            'firstname' => 'Cornelia',
            'lastname' => 'Katzbauer',
            'birth_name' => 'Hergan',
        ], $attributes));
    }

    #[Test]
    public function the_web_form_stores_and_clears_the_birth_name()
    {
        $user = $this->createUser(['create contacts', 'edit contacts']);
        $contact = $this->makeContact($user->id, ['birth_name' => null]);

        $attributes = $contact->toArray();
        $attributes['birth_name'] = 'Hergan';
        unset($attributes['date_of_birth']);

        $this->put(route('contacts.update', $contact->ulid), $attributes)
            ->assertSessionHas('alert-success');
        $this->assertSame('Hergan', $contact->fresh()->birth_name);

        $attributes['birth_name'] = '';

        $this->put(route('contacts.update', $contact->ulid), $attributes)
            ->assertSessionHas('alert-success');
        $this->assertNull($contact->fresh()->birth_name);
    }

    #[Test]
    public function the_api_returns_and_updates_the_birth_name()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);
        $contact = $this->makeContact($user->id);

        $this->getJson(route('api.v1.contacts.show', $contact->ulid))
            ->assertOk()
            ->assertJsonPath('data.birth_name', 'Hergan');

        $this->getJson(route('api.v1.contacts.index'))
            ->assertOk()
            ->assertJsonPath('data.0.birth_name', 'Hergan');

        $this->patchJson(route('api.v1.contacts.update', $contact->ulid), ['birth_name' => 'Huber'])
            ->assertOk()
            ->assertJsonPath('data.birth_name', 'Huber');
    }

    #[Test]
    public function search_finds_a_contact_by_birth_name()
    {
        $user = $this->createUser();
        $this->makeContact($user->id);
        $this->makeContact($user->id, ['firstname' => 'Mario', 'lastname' => 'Rossi', 'birth_name' => null]);

        $this->getJson(route('search.suggest', ['q' => 'Hergan']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.fullname', fn ($name) => str_contains($name, 'Katzbauer'));
    }

    #[Test]
    public function duplicate_search_matches_the_birth_name()
    {
        $user = $this->createUser(['create contacts', 'edit contacts', 'delete contacts']);
        $this->makeContact($user->id);

        $this->getJson(route('duplicates.search', ['q' => 'Hergan']))
            ->assertOk()
            ->assertJsonCount(1);
    }
}
