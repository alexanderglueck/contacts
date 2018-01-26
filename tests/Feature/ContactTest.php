<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_create_a_contact()
    {
        \Session::start();

        $this->withoutMiddleware();

        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->make([
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $parameters = $contact->toArray();
        $parameters['date_of_birth'] = $contact->formatted_date_of_birth;

        $response = $this
            ->actingAs($user)
            ->post(route('contacts.store'), $parameters);

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('contacts', $contact->toArray());
        $this->assertAuthenticatedAs($user);
    }
}
