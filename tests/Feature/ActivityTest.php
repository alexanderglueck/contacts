<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function creating_a_contact_adds_an_activity_entry()
    {
        $user = $this->createUser('create contacts');

        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $this->assertDatabaseHas('activities', [
            'action' => 'created_contact',
            'user_id' => $user->id,
            'object_id' => $contact->id,
            'object_type' => get_class($contact)
        ]);
    }

    /** @test */
    public function updating_a_contact_adds_an_activity_entry()
    {
        $user = $this->createUser([
            'create contacts',
            'edit contacts'
        ]);

        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $this->assertDatabaseHas('activities', [
            'action' => 'created_contact',
            'user_id' => $user->id,
            'object_id' => $contact->id,
            'object_type' => get_class($contact)
        ]);

        $attributes = $contact->toArray();
        $attributes['lastname'] = 'Doe';

        unset($attributes['date_of_birth']);

        $request = $this->put(
            route('contacts.update', $contact->slug),
            $attributes
        );

        $request->assertSessionHas('alert-success');

        $this->assertDatabaseHas('contacts', [
            'lastname' => 'Doe',
            'id' => $contact->id,
        ]);

        $this->assertDatabaseHas('activities', [
            'action' => 'updated_contact',
            'user_id' => $user->id,
            'object_id' => $contact->id,
            'object_type' => get_class($contact)
        ]);
    }

    /** @test */
    public function deleting_a_contact_adds_an_activity_entry()
    {
        $user = $this->createUser([
            'create contacts',
            'delete contacts'
        ]);

        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $this->assertDatabaseHas('activities', [
            'action' => 'created_contact',
            'user_id' => $user->id,
            'object_id' => $contact->id,
            'object_type' => get_class($contact)
        ]);

        $this->delete(route('contacts.destroy', $contact->slug));

        $this->assertDatabaseHas('activities', [
            'action' => 'deleted_contact',
            'user_id' => $user->id,
            'object_id' => $contact->id,
            'object_type' => get_class($contact)
        ]);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
            'lastname' => $contact->name
        ]);
    }

    /** @test */
    public function a_user_can_view_activities()
    {
        $user = $this->createUser('view activities');

        $contact = factory(Contact::class)->create([
            'created_by' => $user->id
        ]);

        $response = $this->get(route('activities.index'));

        $response->assertSee(trans('ui.activities'));
        $response->assertStatus(200);

        $response->assertSee($contact->lastname);
    }
}
