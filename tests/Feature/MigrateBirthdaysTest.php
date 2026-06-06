<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * contacts:migrate-birthdays — moves legacy "Geburtstag" ContactDate rows into
 * the dedicated Contact::$date_of_birth field and removes the source rows.
 */
class MigrateBirthdaysTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_copies_a_geburtstag_into_date_of_birth_and_deletes_the_source_row()
    {
        $user = $this->createUser();

        $contact = create(Contact::class, [
            'firstname' => 'No', 'lastname' => 'Birthday', 'date_of_birth' => null,
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $date = create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'Geburtstag',
            'date' => '1985-07-15', 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->artisan('contacts:migrate-birthdays')->assertSuccessful();

        $this->assertDatabaseMissing('contact_dates', ['id' => $date->id]);
        $this->assertSame('1985-07-15', $contact->fresh()->date_of_birth);
    }

    #[Test]
    public function it_matches_geburtstag_case_insensitively()
    {
        $user = $this->createUser();
        $contact = create(Contact::class, [
            'date_of_birth' => null, 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'GEBURTSTAG',
            'date' => '1985-07-15', 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->artisan('contacts:migrate-birthdays')->assertSuccessful();

        $this->assertSame('1985-07-15', $contact->fresh()->date_of_birth);
    }

    #[Test]
    public function it_does_not_overwrite_an_existing_differing_date_of_birth()
    {
        $user = $this->createUser();
        $contact = create(Contact::class, [
            'date_of_birth' => '1990-01-01', 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $date = create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'Geburtstag',
            'date' => '1985-07-15', 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->artisan('contacts:migrate-birthdays')->assertSuccessful();

        // Untouched: original date kept, source row preserved for manual review.
        $this->assertSame('1990-01-01', $contact->fresh()->date_of_birth);
        $this->assertDatabaseHas('contact_dates', ['id' => $date->id]);
    }

    #[Test]
    public function dry_run_writes_nothing()
    {
        $user = $this->createUser();
        $contact = create(Contact::class, [
            'date_of_birth' => null, 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $date = create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'Geburtstag',
            'date' => '1985-07-15', 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->artisan('contacts:migrate-birthdays --dry-run')->assertSuccessful();

        $this->assertNull($contact->fresh()->date_of_birth);
        $this->assertDatabaseHas('contact_dates', ['id' => $date->id]);
    }

    #[Test]
    public function it_is_idempotent()
    {
        $user = $this->createUser();
        $contact = create(Contact::class, [
            'date_of_birth' => null, 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $contact->id, 'name' => 'Geburtstag',
            'date' => '1985-07-15', 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->artisan('contacts:migrate-birthdays')->assertSuccessful();
        $this->artisan('contacts:migrate-birthdays')->assertSuccessful();

        $this->assertSame('1985-07-15', $contact->fresh()->date_of_birth);
        $this->assertSame(0, ContactDate::whereRaw('LOWER(name) = ?', ['geburtstag'])->count());
    }
}
