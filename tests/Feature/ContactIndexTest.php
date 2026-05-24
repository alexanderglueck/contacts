<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Reads (index, show, partial reloads) for contacts and their
 * sub-resources. The contact's own page is what serves up
 * sub-resource data via Inertia::optional() lazy props, so testing
 * "did the addresses tile actually load addresses?" means making a
 * partial-reload request against `contacts.show`.
 */
class ContactIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Make an Inertia partial-reload request asking for specific props.
     *
     * Match the server's asset version (computed from the build manifest)
     * so the middleware doesn't bail out with a 409 version-mismatch
     * redirect instead of evaluating the partial.
     */
    private function partialReload(string $url, string $component, array $only)
    {
        $manifest = public_path('build/manifest.json');
        $version = file_exists($manifest) ? hash_file('xxh128', $manifest) : '';

        return $this->withHeaders([
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $version,
            'X-Inertia-Partial-Component' => $component,
            'X-Inertia-Partial-Data' => implode(',', $only),
            'Accept' => 'application/json',
        ])->get($url);
    }

    #[Test]
    public function the_contact_index_lists_the_current_users_contacts()
    {
        $user = $this->createUser();
        create(Contact::class, [
            'firstname' => 'Alice',
            'lastname' => 'Bond',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        create(Contact::class, [
            'firstname' => 'Bob',
            'lastname' => 'Sled',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->get(route('contacts.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Contacts/Index')
            ->has('contacts.data', 2)
        );
    }

    #[Test]
    public function the_contact_index_does_not_leak_contacts_from_another_tenant()
    {
        $alice = $this->createUser();
        create(Contact::class, [
            'firstname' => 'AliceContact',
            'created_by' => $alice->id,
            'updated_by' => $alice->id,
        ]);

        // Switch auth + tenant to a brand-new user; their index should
        // be empty even though Alice's contact still exists in the DB.
        $bob = $this->createUser();

        $response = $this->get(route('contacts.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Contacts/Index')
            ->has('contacts.data', 0)
        );
        // Sanity check: the row really does exist server-side.
        $this->assertDatabaseHas('contacts', ['firstname' => 'AliceContact']);
    }

    #[Test]
    public function the_contact_index_search_filters_by_query()
    {
        $user = $this->createUser();
        create(Contact::class, [
            'firstname' => 'Zelda',
            'lastname' => 'Ferguson',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        create(Contact::class, [
            'firstname' => 'Mario',
            'lastname' => 'Rossi',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->get(route('contacts.index', ['q' => 'Ferguson']));

        $response->assertInertia(fn (Assert $page) => $page
            ->where('q', 'Ferguson')
            ->has('contacts.data', 1)
            ->where('contacts.data.0.fullname', fn ($name) => str_contains($name, 'Ferguson'))
        );
    }

    #[Test]
    public function the_contact_show_returns_the_count_fields_for_each_section()
    {
        $contact = $this->createContactWithSubResources();

        $response = $this->get(route('contacts.show', $contact->ulid));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Contacts/Show')
            ->where('contact.ulid', $contact->ulid)
            ->where('contact.numbers_count', 2)
            ->where('contact.emails_count', 1)
            ->where('contact.addresses_count', 1)
            ->where('contact.notes_count', 1)
            ->where('contact.urls_count', 0)
        );
    }

    #[Test]
    public function loading_the_show_page_without_partial_reload_omits_optional_props()
    {
        $contact = $this->createContactWithSubResources();

        $response = $this->get(route('contacts.show', $contact->ulid));

        // Inertia::optional props are skipped on a fresh visit — the
        // slideovers fetch them on demand. The count fields still come
        // back so the tiles can render.
        $response->assertInertia(fn (Assert $page) => $page
            ->missing('numbers')
            ->missing('emails')
            ->missing('addresses')
        );
    }

    #[Test]
    public function partial_reload_for_numbers_returns_the_contacts_phone_numbers()
    {
        $contact = $this->createContactWithSubResources();

        $response = $this->partialReload(
            route('contacts.show', $contact->ulid),
            'Contacts/Show',
            ['numbers'],
        );

        // Partial reloads return JSON rather than a rendered view, so
        // we read straight from the response body instead of going
        // through assertInertia (which expects the view-data variant).
        $response->assertOk();
        $this->assertSame('Contacts/Show', $response->json('component'));
        $this->assertCount(2, $response->json('props.numbers'));
        $response->assertJsonPath('props.numbers.0.number', '+43 660 1111111');
        $response->assertJsonPath('props.numbers.1.number', '+43 660 2222222');
    }

    #[Test]
    public function partial_reload_for_emails_returns_the_contacts_emails()
    {
        $contact = $this->createContactWithSubResources();

        $response = $this->partialReload(
            route('contacts.show', $contact->ulid),
            'Contacts/Show',
            ['emails'],
        );

        $response->assertOk();
        $this->assertCount(1, $response->json('props.emails'));
        $response->assertJsonPath('props.emails.0.email', 'jane@example.com');
    }

    #[Test]
    public function partial_reload_for_addresses_returns_the_contacts_addresses_and_countries()
    {
        $contact = $this->createContactWithSubResources();

        $response = $this->partialReload(
            route('contacts.show', $contact->ulid),
            'Contacts/Show',
            ['addresses', 'countries'],
        );

        $response->assertOk();
        $this->assertCount(1, $response->json('props.addresses'));
        $response->assertJsonPath('props.addresses.0.city', 'Graz');
        $this->assertNotEmpty($response->json('props.countries'));
    }

    #[Test]
    public function partial_reload_does_not_leak_sub_resources_from_another_contact()
    {
        $user = $this->createUser();
        $own = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $own->numbers()->create(['name' => 'Own', 'number' => '+43 660 0000001']);

        $other = create(Contact::class, ['created_by' => $user->id, 'updated_by' => $user->id]);
        $other->numbers()->create(['name' => 'Other A', 'number' => '+43 660 0000099']);
        $other->numbers()->create(['name' => 'Other B', 'number' => '+43 660 0000098']);

        $response = $this->partialReload(
            route('contacts.show', $own->ulid),
            'Contacts/Show',
            ['numbers'],
        );

        // Only the contact we asked for — the other contact's two
        // numbers must not appear.
        $response->assertOk();
        $this->assertCount(1, $response->json('props.numbers'));
        $response->assertJsonPath('props.numbers.0.number', '+43 660 0000001');
    }

    private function createContactWithSubResources(): Contact
    {
        $user = $this->createUser();

        $contact = create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $contact->numbers()->create(['name' => 'Mobile', 'number' => '+43 660 1111111']);
        $contact->numbers()->create(['name' => 'Work', 'number' => '+43 660 2222222']);
        $contact->emails()->create(['name' => 'Personal', 'email' => 'jane@example.com']);
        $contact->notes()->create(['name' => 'Intro', 'note' => 'Met at the conference.']);
        $contact->addresses()->create([
            'name' => 'Home',
            'street' => 'Hauptstraße 1',
            'zip' => '8010',
            'city' => 'Graz',
            'state' => 'Steiermark',
            'country_id' => DB::table('countries')->value('id'),
        ]);

        return $contact;
    }
}
