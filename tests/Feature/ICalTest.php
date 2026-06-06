<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactDate;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * /calendar/ical — the iCal subscription feed. Must require authentication,
 * stay scoped to the caller's team, and emit all-day VALUE=DATE events (no
 * time component) for both birthdays and important dates.
 */
class ICalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_feed_requires_authentication()
    {
        $this->getJson(route('ical'))->assertStatus(401);
    }

    #[Test]
    public function the_feed_includes_both_birthdays_and_important_dates_as_all_day_events()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        // Birthday in the feed window (30 days back → 9 months forward).
        $soon = (new \DateTime('today'))->modify('+5 days');
        create(Contact::class, [
            'firstname' => 'Birthday', 'lastname' => 'Person',
            'date_of_birth' => '1990-'.$soon->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $datedContact = create(Contact::class, [
            'firstname' => 'Wedding', 'lastname' => 'Couple',
            'date_of_birth' => null,
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        create(ContactDate::class, [
            'contact_id' => $datedContact->id,
            'name' => 'Hochzeitstag',
            'date' => '2010-'.$soon->format('m-d'),
            'skip_year' => false,
            'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        $body = $this->get(route('ical'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->getContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $body);
        // Birthday label is the translated date_of_birth string for the locale.
        $this->assertStringContainsString(trans('ui.date_of_birth'), $body);
        $this->assertStringContainsString('Hochzeitstag', $body);

        // All-day events render as DTSTART;VALUE=DATE:YYYYMMDD — never with a
        // time component or a TZID, which is what would cause an hour offset.
        $this->assertMatchesRegularExpression('/DTSTART;VALUE=DATE:\d{8}/', $body);
        $this->assertStringNotContainsString('DTSTART;TZID', $body);
        $this->assertDoesNotMatchRegularExpression('/DTSTART[^\r\n]*T\d{6}/', $body);
    }

    #[Test]
    public function a_real_sanctum_token_in_the_query_authenticates_the_feed()
    {
        // Exercises the actual Google-Calendar path: a personal access token
        // passed as ?api_token= (with its literal "|" url-encoded to %7C) on a
        // stateless request with no session — not the Sanctum::actingAs shortcut.
        $user = $this->createUser();
        $team = $user->currentTeam;
        $token = $user->createToken('Calendar sync')->plainTextToken;

        // Fully detach session/web auth so only the token can authenticate —
        // otherwise Sanctum's session-guard fallback would mask the token path.
        auth()->guard('web')->logout();
        session()->flush();

        $body = $this->get(route('ical', ['api_token' => $token, 'tenant' => $team->uuid]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $body);
    }

    #[Test]
    public function a_revoked_or_unknown_token_is_rejected()
    {
        $user = $this->createUser();
        $team = $user->currentTeam;
        $token = $user->createToken('Calendar sync')->plainTextToken;

        // Simulate a rotation/revocation after the client subscribed, and a
        // stateless external request (no web session to fall back on).
        $user->tokens()->delete();
        auth()->guard('web')->logout();
        session()->flush();

        // Guests are redirected to login (302) rather than served the feed.
        $this->get(route('ical', ['api_token' => $token, 'tenant' => $team->uuid]))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function the_feed_is_scoped_to_the_callers_team()
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $soon = (new \DateTime('today'))->modify('+3 days');

        create(Contact::class, [
            'firstname' => 'Mine', 'lastname' => 'Contact',
            'date_of_birth' => '1990-'.$soon->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);

        // A contact belonging to a different team must never appear.
        $otherTeam = create(Team::class, ['owner_id' => $user->id]);
        $foreign = create(Contact::class, [
            'firstname' => 'Foreign', 'lastname' => 'Contact',
            'date_of_birth' => '1990-'.$soon->format('m-d'),
            'active' => 1, 'created_by' => $user->id, 'updated_by' => $user->id,
        ]);
        DB::table('contacts')->where('id', $foreign->id)->update(['team_id' => $otherTeam->id]);

        $body = $this->get(route('ical'))->assertOk()->getContent();

        $this->assertStringContainsString('Mine Contact', $body);
        $this->assertStringNotContainsString('Foreign Contact', $body);
    }
}
