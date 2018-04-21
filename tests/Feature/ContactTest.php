<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_create_a_contact()
    {
        $user = $this->createUser('create contacts');

        $contact = make(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $parameters = $contact->toArray();
        $parameters['date_of_birth'] = $contact->formatted_date_of_birth;

        $response = $this
            ->post(route('contacts.store'), $parameters);

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('contacts', $contact->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_a_contact_created_by_him()
    {
        $user = $this->createUser('view contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id
        ]);

        $response = $this->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_not_view_a_contact_created_by_another_user()
    {
        $user = $this->createUser('view contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id
        ]);

        $anotherTeam = create(Team::class);
        $viewer = create(User::class, [
            'current_team_id' => $anotherTeam->id
        ]);

        $response = $this->actingAs($viewer)
            ->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(404);
        $this->assertAuthenticatedAs($viewer);

        $response = $this->actingAs($user)
            ->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_delete_view()
    {
        $user = $this->createUser('delete contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id
        ]);

        $response = $this->get(route('contacts.delete', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_delete_a_contact()
    {
        $user = $this->createUser('delete contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id
        ]);

        $this->assertDatabaseHas('contacts', $contact->toArray());

        $response = $this
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
        $user = $this->createUser('edit contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id
        ]);

        $response = $this->get(route('contacts.edit', [$contact->slug]));

        $response->assertStatus(200);
        $response->assertSee('Kontakt bearbeiten');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_update_a_contact()
    {
        $user = $this->createUser('edit contacts');

        $contactGroup = create(Contact::class, [
            'created_by' => $user->id
        ]);

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

        $response = $this
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
