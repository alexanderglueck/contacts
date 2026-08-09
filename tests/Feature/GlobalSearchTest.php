<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The nav search box (available on every page, including the dashboard)
 * feeds off search.suggest — a JSON typeahead that hits the same Scout
 * index as contacts.index.
 */
class GlobalSearchTest extends TestCase
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
    public function suggest_returns_contacts_matching_the_term()
    {
        $user = $this->createUser();
        $this->makeContact($user->id, ['firstname' => 'Zelda', 'lastname' => 'Ferguson']);
        $this->makeContact($user->id, ['firstname' => 'Mario', 'lastname' => 'Rossi']);

        $response = $this->getJson(route('search.suggest', ['q' => 'Ferguson']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.fullname', fn ($name) => str_contains($name, 'Ferguson'));

        $this->assertArrayHasKey('ulid', $response->json('data.0'));
    }

    #[Test]
    public function suggest_returns_nothing_for_a_blank_term()
    {
        $user = $this->createUser();
        $this->makeContact($user->id, ['firstname' => 'Zelda', 'lastname' => 'Ferguson']);

        $this->getJson(route('search.suggest', ['q' => '   ']))
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');

        $this->getJson(route('search.suggest'))
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    #[Test]
    public function suggest_does_not_leak_contacts_from_another_tenant()
    {
        $alice = $this->createUser();
        $this->makeContact($alice->id, ['firstname' => 'Alice', 'lastname' => 'Zzytestsson']);

        // A fresh user gets their own team/tenant; Alice's contact must not
        // show up in their suggestions.
        $bob = $this->createUser();

        $this->getJson(route('search.suggest', ['q' => 'Zzytestsson']))
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');

        $this->makeContact($bob->id, ['firstname' => 'Bob', 'lastname' => 'Zzytestsson']);

        $this->getJson(route('search.suggest', ['q' => 'Zzytestsson']))
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.fullname', fn ($name) => str_contains($name, 'Bob'));
    }

    #[Test]
    public function suggest_caps_the_number_of_suggestions()
    {
        $user = $this->createUser();

        for ($i = 0; $i < 12; $i++) {
            $this->makeContact($user->id, ['firstname' => 'Xandra'.$i, 'lastname' => 'Suggestable']);
        }

        $this->getJson(route('search.suggest', ['q' => 'Suggestable']))
            ->assertStatus(200)
            ->assertJsonCount(8, 'data');
    }

    #[Test]
    public function suggest_requires_authentication()
    {
        $this->getJson(route('search.suggest', ['q' => 'anything']))
            ->assertStatus(401);
    }
}
