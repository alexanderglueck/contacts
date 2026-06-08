<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /api/v1/calendar — events feed (FullCalendar-style window query),
 * upcoming-events widget feed, and the iCal subscription URL CRUD.
 */
class CalendarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function events_returns_a_birthday_falling_inside_the_window()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        create(Contact::class, [
            'firstname' => 'Birthday',
            'lastname' => 'Person',
            'date_of_birth' => '1990-05-25',
            'active' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.events', [
            'from' => '2026-05-01',
            'to' => '2026-05-31',
        ]));

        $response->assertOk();
        $events = $response->json('data');
        $this->assertNotEmpty($events);
        $this->assertContains('birthday', collect($events)->pluck('type')->all());

        $birthday = collect($events)->firstWhere('type', 'birthday');
        $this->assertSame('2026-05-25', $birthday['date']);
        // 1990 → 2026 = 36th birthday.
        $this->assertSame(36, $birthday['years']);
    }

    #[Test]
    public function events_suppresses_the_age_for_an_unknown_year_1900_birthday()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        create(Contact::class, [
            'firstname' => 'Unknown',
            'lastname' => 'Year',
            // 1900 = "year unknown" sentinel — no meaningful age.
            'date_of_birth' => '1900-05-25',
            'active' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.events', [
            'from' => '2026-05-01',
            'to' => '2026-05-31',
        ]));

        $response->assertOk();
        $birthday = collect($response->json('data'))->firstWhere('type', 'birthday');
        $this->assertSame('2026-05-25', $birthday['date']);
        $this->assertTrue($birthday['skip_year']);
        $this->assertNull($birthday['years']);
    }

    #[Test]
    public function events_excludes_birthdays_outside_the_window()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        create(Contact::class, [
            'firstname' => 'November',
            'date_of_birth' => '1990-11-15',
            'active' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.events', [
            'from' => '2026-05-01',
            'to' => '2026-05-31',
        ]));

        $response->assertOk();
        $this->assertSame([], $response->json('data'));
    }

    #[Test]
    public function events_returns_an_unbounded_year_window_after_the_listyear_fix()
    {
        // listYear sends a 365-day span (e.g. 2026-01-01 → 2027-01-01)
        // where both MMDDs are '0101'. The model layer's "span ≥ 365 days
        // → skip MMDD filter" branch is what makes this not collapse to
        // "only Jan 1" — this test guards that branch from regressing.
        $user = $this->createUser();
        Sanctum::actingAs($user);

        create(Contact::class, [
            'firstname' => 'May', 'date_of_birth' => '1990-05-25', 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(Contact::class, [
            'firstname' => 'September', 'date_of_birth' => '1990-09-10', 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(Contact::class, [
            'firstname' => 'December', 'date_of_birth' => '1990-12-31', 'active' => 1,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.events', [
            'from' => '2026-01-01',
            'to' => '2027-01-01',
        ]));

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function events_rejects_missing_or_invalid_date_params()
    {
        Sanctum::actingAs($this->createUser());

        $this->getJson(route('api.v1.calendar.events'))->assertStatus(422);
        $this->getJson(route('api.v1.calendar.events', ['from' => 'nope', 'to' => 'nope']))
            ->assertStatus(422);
        // `to` must be on/after `from`.
        $this->getJson(route('api.v1.calendar.events', [
            'from' => '2026-06-01',
            'to' => '2026-05-01',
        ]))->assertStatus(422);
    }

    #[Test]
    public function upcoming_includes_a_birthday_within_the_next_seven_days()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        // today + 3 days
        $soon = (new \DateTime('today'))->modify('+3 days');

        create(Contact::class, [
            'firstname' => 'Soon',
            'date_of_birth' => '1990-'.$soon->format('m-d'),
            'active' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.upcoming'));

        $response->assertOk();
        $events = collect($response->json('data'));
        $this->assertCount(1, $events);
        $this->assertSame(3, $events->first()['days_until']);
        $this->assertFalse($events->first()['is_today']);
    }

    #[Test]
    public function upcoming_flags_a_today_birthday_as_is_today()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $today = new \DateTime('today');

        create(Contact::class, [
            'firstname' => 'Today',
            'date_of_birth' => '1990-'.$today->format('m-d'),
            'active' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->getJson(route('api.v1.calendar.upcoming'));

        $response->assertOk();
        $event = collect($response->json('data'))->first();
        $this->assertSame(0, $event['days_until']);
        $this->assertTrue($event['is_today']);
    }

    #[Test]
    public function sync_url_reports_no_token_when_the_user_has_never_rotated_one()
    {
        Sanctum::actingAs($this->createUser());

        $response = $this->getJson(route('api.v1.calendar.sync_url'));

        $response->assertOk();
        $this->assertFalse($response->json('data.configured'));
        $this->assertNull($response->json('data.url'));
    }

    #[Test]
    public function rotating_the_sync_url_returns_a_plaintext_url_and_persists_a_token()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.calendar.sync_url.rotate'));

        $response->assertOk();
        $this->assertTrue($response->json('data.configured'));
        $this->assertNotEmpty($response->json('data.url'));
        $this->assertStringContainsString('api_token=', $response->json('data.url'));
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Calendar sync',
        ]);
    }

    #[Test]
    public function rotating_again_revokes_the_old_token()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson(route('api.v1.calendar.sync_url.rotate'));
        $firstTokenId = $user->tokens()->where('name', 'Calendar sync')->value('id');

        $this->postJson(route('api.v1.calendar.sync_url.rotate'));

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $firstTokenId]);
        $this->assertSame(1, $user->tokens()->where('name', 'Calendar sync')->count());
    }

    #[Test]
    public function revoke_sync_url_removes_the_token_row()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->postJson(route('api.v1.calendar.sync_url.rotate'));
        $this->assertSame(1, $user->tokens()->where('name', 'Calendar sync')->count());

        $this->deleteJson(route('api.v1.calendar.sync_url.revoke'))
            ->assertOk()
            ->assertJsonPath('data.configured', false);

        $this->assertSame(0, $user->tokens()->where('name', 'Calendar sync')->count());
    }

    #[Test]
    public function calendar_endpoints_require_authentication()
    {
        $this->getJson(route('api.v1.calendar.events', ['from' => '2026-05-01', 'to' => '2026-05-31']))
            ->assertStatus(401);
        $this->getJson(route('api.v1.calendar.upcoming'))->assertStatus(401);
        $this->getJson(route('api.v1.calendar.sync_url'))->assertStatus(401);
        $this->postJson(route('api.v1.calendar.sync_url.rotate'))->assertStatus(401);
        $this->deleteJson(route('api.v1.calendar.sync_url.revoke'))->assertStatus(401);
    }
}
