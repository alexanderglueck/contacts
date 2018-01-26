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

    /** @test */
    public function a_user_can_view_a_contact_created_by_him()
    {
        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_not_view_a_contact_created_by_another_user()
    {
        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $viewer = factory(User::class)->create();

        $response = $this->actingAs($viewer)
            ->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(403);
        $this->assertAuthenticatedAs($viewer);

        $response = $this->actingAs($user)
            ->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_delete_view()
    {
        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->create();

        $response = $this->actingAs($user)
            ->get(route('contacts.delete', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_delete_a_contact()
    {
        \Session::start();

        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->create();

        $this->assertDatabaseHas('contacts', $contact->toArray());

        $response = $this->actingAs($user)
            ->delete(route('contacts.destroy', [$contact->slug]), [
                '_token' => csrf_token()
            ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseMissing('contacts', $contact->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_edit_view()
    {
        $user = factory(User::class)->create();
        $contact = factory(Contact::class)->create();

        $response = $this->actingAs($user)
            ->get(route('contacts.edit', [$contact->slug]));

        $response->assertStatus(200);
        $response->assertSee('Kontakt bearbeiten');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_update_a_contact()
    {
        \Session::start();

        $user = factory(User::class)->create();
        $contactGroup = factory(Contact::class)->create();

        $check = [
            'id' => $contactGroup->id,
            'lastname' => $contactGroup->lastname,
        ];

        $contactGroup2 = $contactGroup;
        $contactGroup2->lastname = "something else";
        $contactGroup2->date_of_birth = $contactGroup2->formatted_date_of_birth;

        $parameters = $contactGroup2->toArray();
        $parameters['date_of_birth'] = $contactGroup2->formatted_date_of_birth;

        unset($parameters['updated_at']);
        unset($contactGroup2['updated_at']);
        unset($parameters['updated_by']);
        unset($contactGroup2['updated_by']);

        $response = $this->actingAs($user)
            ->put(route('contacts.update', [$contactGroup->slug]),
                array_merge($parameters,
                    ['_token' => csrf_token()]
                ));


        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseHas('contacts', $contactGroup2->toArray());
        $this->assertDatabaseMissing('contacts', $check);
        $this->assertAuthenticatedAs($user);
    }
}
