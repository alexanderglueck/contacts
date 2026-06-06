<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The contact page surfaces a reminder banner when that contact's birthday or
 * one of its important dates falls on today — scoped to that contact only.
 */
class ContactTodaysEventsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_includes_the_contacts_birthday_when_it_is_today()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        $contact = create(Contact::class, [
            'date_of_birth' => '1990-'.$today->format('m-d'),
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->get(route('contacts.show', $contact->ulid))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Contacts/Show')
                ->has('todaysEvents', 1)
                ->where('todaysEvents.0.type', 'birthday'));
    }

    #[Test]
    public function it_includes_an_important_date_when_it_is_today()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        $contact = create(Contact::class, [
            'date_of_birth' => null,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'Hochzeitstag',
            'date' => '2010-'.$today->format('m-d'), 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->get(route('contacts.show', $contact->ulid))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('todaysEvents', 1)
                ->where('todaysEvents.0.type', 'date')
                ->where('todaysEvents.0.label', fn ($label) => str_contains($label, 'Hochzeitstag')));
    }

    #[Test]
    public function it_includes_a_death_anniversary_when_it_is_today()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        $contact = create(Contact::class, [
            'date_of_birth' => null,
            'died_at' => '2015-'.$today->format('m-d'),
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->get(route('contacts.show', $contact->ulid))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('todaysEvents', 1)
                ->where('todaysEvents.0.type', 'memorial'));
    }

    #[Test]
    public function it_is_empty_when_nothing_falls_on_today()
    {
        $user = $this->createUser();
        $tomorrow = (new \DateTime('today'))->modify('+1 day');

        $contact = create(Contact::class, [
            'date_of_birth' => '1990-'.$tomorrow->format('m-d'),
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->get(route('contacts.show', $contact->ulid))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->has('todaysEvents', 0));
    }
}
