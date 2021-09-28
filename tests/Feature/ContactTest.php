<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

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
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $response = $this->get(route('contacts.show', [$contact->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_delete_view()
    {
        $user = $this->createUser('delete contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
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
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertModelExists($contact);

        $response = $this
            ->delete(route('contacts.destroy', [$contact->slug]), [
                '_token' => csrf_token()
            ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertModelMissing($contact);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_edit_view()
    {
        $user = $this->createUser('edit contacts');

        $contact = create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
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

        $contact = create(Contact::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $check = [
            'id' => $contact->id,
            'lastname' => $contact->lastname,
        ];

        $editedContact = $contact;
        $editedContact->lastname = "something else";
        $editedContact->date_of_birth = $editedContact->formatted_date_of_birth;

        $parameters = $editedContact->toArray();
        $parameters['date_of_birth'] = $editedContact->formatted_date_of_birth;

        unset($parameters['updated_at']);
        unset($editedContact['updated_at']);
        unset($parameters['updated_by']);
        unset($editedContact['updated_by']);

        $response = $this->actingAs($user)
            ->put(route('contacts.update', [$contact->slug]),
                $parameters);


        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertModelExists($editedContact);
        $this->assertDatabaseMissing('contacts', $check);
        $this->assertAuthenticatedAs($user);
    }
}
