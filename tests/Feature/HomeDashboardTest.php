<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The dashboard surfaces both birthdays and important dates (not just
 * birthdays, which was the regression that prompted this work).
 */
class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function todays_events_include_both_birthdays_and_important_dates()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        create(Contact::class, [
            'firstname' => 'Birthday', 'lastname' => 'Person',
            'date_of_birth' => '1990-'.$today->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $c = create(Contact::class, [
            'firstname' => 'Wedding', 'lastname' => 'Couple', 'date_of_birth' => null,
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $c->id, 'name' => 'Hochzeitstag',
            'date' => '2010-'.$today->format('m-d'), 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Home')
                ->has('todaysEvents', 2)
                ->where('todaysEvents.0.type', fn ($type) => in_array($type, ['birthday', 'date'], true)));
    }
}
