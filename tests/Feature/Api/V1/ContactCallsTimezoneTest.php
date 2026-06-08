<?php

namespace Tests\Feature\Api\V1;

use App\Models\Contact;
use App\Models\ContactCall;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * called_at is stored in UTC and exchanged with clients as UTC ISO-8601; each
 * client localizes for display. These cover the server's half of that contract:
 * incoming instants land in UTC regardless of how they were expressed, and the
 * value read back is the same instant.
 */
class ContactCallsTimezoneTest extends TestCase
{
    use RefreshDatabase;

    private function aContact(): Contact
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        return create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    #[Test]
    public function it_stores_a_utc_instant_and_returns_the_same_instant()
    {
        $contact = $this->aContact();

        $response = $this->postJson(route('api.v1.contacts.calls.store', $contact->ulid), [
            'called_at' => '2026-05-24T18:00:00Z',
        ]);

        $response->assertCreated();

        // Round-trips to the same instant (format may carry microseconds).
        $this->assertTrue(
            Carbon::parse($response->json('data.called_at'))->equalTo(Carbon::parse('2026-05-24T18:00:00Z'))
        );

        // Persisted in UTC.
        $call = ContactCall::first();
        $this->assertSame('2026-05-24 18:00:00', $call->called_at->utc()->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_normalizes_an_offset_bearing_value_to_utc()
    {
        $contact = $this->aContact();

        // 20:00 in a +02:00 zone is 18:00 UTC, regardless of any server/user tz.
        $this->postJson(route('api.v1.contacts.calls.store', $contact->ulid), [
            'called_at' => '2026-05-24T20:00:00+02:00',
        ])->assertCreated();

        $this->assertSame(
            '2026-05-24 18:00:00',
            ContactCall::first()->called_at->utc()->format('Y-m-d H:i:s')
        );
    }
}
