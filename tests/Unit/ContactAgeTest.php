<?php

namespace Tests\Unit;

use App\Models\Contact;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tests\CreatesApplication;

/**
 * The age accessor never touches the database, but booting the model does pull
 * Scout in — hence a booted app, and not Tests\TestCase with its db:seed.
 */
class ContactAgeTest extends TestCase
{
    use CreatesApplication;

    private function contact(?string $dateOfBirth, ?string $diedAt = null): Contact
    {
        $contact = new Contact();
        $contact->date_of_birth = $dateOfBirth;
        $contact->died_at = $diedAt;

        return $contact;
    }

    private function daysFromToday(int $days): string
    {
        return (new \DateTime('today'))
            ->modify($days >= 0 ? "+{$days} days" : "{$days} days")
            ->format('Y-m-d');
    }

    #[Test]
    public function it_counts_whole_years_since_the_birthday()
    {
        $dob = (new \DateTime('today'))->modify('-30 years')->modify('-3 days');

        $this->assertSame(30, $this->contact($dob->format('Y-m-d'))->age);
    }

    #[Test]
    public function the_birthday_itself_already_counts_as_a_full_year()
    {
        // Regression guard: parsing the DOB without zeroing the time leaves it
        // hours short of the full year and reports 29 here.
        $dob = (new \DateTime('today'))->modify('-30 years');

        $this->assertSame(30, $this->contact($dob->format('Y-m-d'))->age);
    }

    #[Test]
    public function a_birthday_still_to_come_this_year_is_a_year_less()
    {
        $dob = (new \DateTime('today'))->modify('-30 years')->modify('+3 days');

        $this->assertSame(29, $this->contact($dob->format('Y-m-d'))->age);
    }

    #[Test]
    public function an_infant_is_zero_rather_than_null()
    {
        $this->assertSame(0, $this->contact($this->daysFromToday(-10))->age);
    }

    #[Test]
    public function it_has_no_age_without_a_date_of_birth()
    {
        $this->assertNull($this->contact(null)->age);
    }

    #[Test]
    public function it_has_no_age_when_the_birth_year_is_the_unknown_sentinel()
    {
        $this->assertNull($this->contact('1900-03-12')->age);
    }

    #[Test]
    public function a_deceased_contact_reports_the_age_they_reached()
    {
        // Born 1950-06-01, died 2000-05-01 — a month short of turning 50.
        $this->assertSame(49, $this->contact('1950-06-01', '2000-05-01')->age);
    }

    #[Test]
    public function a_death_on_the_birthday_counts_that_birthday()
    {
        $this->assertSame(50, $this->contact('1950-06-01', '2000-06-01')->age);
    }

    #[Test]
    public function it_has_no_age_when_the_date_of_birth_lies_in_the_future()
    {
        $this->assertNull($this->contact($this->daysFromToday(10))->age);
    }

    #[Test]
    public function it_has_no_age_when_death_precedes_birth()
    {
        $this->assertNull($this->contact('1990-01-01', '1980-01-01')->age);
    }
}
