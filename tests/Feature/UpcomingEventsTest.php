<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactDate;
use App\Models\Team;
use App\Services\UpcomingEvent;
use App\Services\UpcomingEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * UpcomingEvents — the shared source of truth that merges birthdays
 * (date_of_birth) and important dates (ContactDate) for every surface.
 */
class UpcomingEventsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function events_on_date_returns_both_a_birthday_and_an_important_date()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        create(Contact::class, [
            'firstname' => 'Cake', 'lastname' => 'Eater',
            'date_of_birth' => '1990-'.$today->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        $c = create(Contact::class, [
            'firstname' => 'Anniversary', 'lastname' => 'Couple', 'date_of_birth' => null,
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $c->id, 'name' => 'Hochzeitstag',
            'date' => '2010-'.$today->format('m-d'), 'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $events = UpcomingEvents::eventsOnDate($today);

        $this->assertCount(2, $events);
        $types = $events->pluck('type')->all();
        $this->assertContains(UpcomingEvent::TYPE_BIRTHDAY, $types);
        $this->assertContains(UpcomingEvent::TYPE_DATE, $types);
    }

    #[Test]
    public function events_on_date_includes_a_death_anniversary()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        create(Contact::class, [
            'firstname' => 'Memorial', 'date_of_birth' => null,
            'died_at' => '2015-'.$today->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $events = UpcomingEvents::eventsOnDate($today);

        $this->assertContains(UpcomingEvent::TYPE_MEMORIAL, $events->pluck('type')->all());
        $memorial = $events->firstWhere('type', UpcomingEvent::TYPE_MEMORIAL);
        $this->assertStringContainsString(trans('ui.death_anniversary'), $memorial->label((int) date('Y')));
    }

    #[Test]
    public function events_in_range_handles_a_window_crossing_the_year_boundary()
    {
        $user = $this->createUser();

        // A late-December birthday inside a Dec → Mar window must surface even
        // though from-MMDD (e.g. 1201) is numerically greater than to-MMDD.
        create(Contact::class, [
            'firstname' => 'December', 'date_of_birth' => '1990-12-28',
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $start = new \DateTime('2026-12-01');
        $end = new \DateTime('2027-03-01');

        $events = UpcomingEvents::eventsInRange($start, $end);

        $this->assertCount(1, $events);
        $this->assertTrue($events->first()->isBirthday());
    }

    #[Test]
    public function events_are_scoped_to_the_current_team()
    {
        $user = $this->createUser();
        $today = new \DateTime('today');

        create(Contact::class, [
            'firstname' => 'Mine', 'date_of_birth' => '1990-'.$today->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $otherTeam = create(Team::class, ['owner_id' => $user->id]);
        $foreign = create(Contact::class, [
            'firstname' => 'Foreign', 'date_of_birth' => '1990-'.$today->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        DB::table('contacts')->where('id', $foreign->id)->update(['team_id' => $otherTeam->id]);

        $events = UpcomingEvents::eventsOnDate($today);

        $this->assertCount(1, $events);
        $this->assertSame('Mine', $events->first()->contact->firstname);
    }
}
