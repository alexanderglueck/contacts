<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ContactGroup;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactGroupTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_create_a_contact_group()
    {
        \Session::start();

        $this->withoutMiddleware();

        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->make([
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertDatabaseMissing('contact_groups', $contactGroup->toArray());

        $response = $this
            ->actingAs($user)
            ->post(route('contact_groups.store'), $contactGroup->toArray());

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('contact_groups', $contactGroup->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_a_contact_group()
    {
        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->create();

        $response = $this->actingAs($user)
            ->get(route('contact_groups.show', [$contactGroup->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_group_delete_view()
    {
        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->create();

        $response = $this->actingAs($user)
            ->get(route('contact_groups.delete', [$contactGroup->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_delete_a_contact_group()
    {
        \Session::start();

        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->create();

        $this->assertDatabaseHas('contact_groups', $contactGroup->toArray());

        $response = $this->actingAs($user)
            ->delete(route('contact_groups.destroy', [$contactGroup->slug]), [
                '_token' => csrf_token()
            ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseMissing('contact_groups', $contactGroup->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_contact_group_edit_view()
    {
        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->create();

        $response = $this->actingAs($user)
            ->get(route('contact_groups.edit', [$contactGroup->slug]));

        $response->assertStatus(200);
        $response->assertSee('Kontaktgruppe bearbeiten');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_update_a_contact_group()
    {
        \Session::start();

        $user = factory(User::class)->create();
        $contactGroup = factory(ContactGroup::class)->create();

        $check = $contactGroup->toArray();

        $contactGroup2 = $contactGroup;
        $contactGroup2->name = "something else";

        unset($contactGroup2->updated_at);
        unset($contactGroup2->updated_by);
        unset($contactGroup->updated_at);
        unset($contactGroup->updated_by);

        $response = $this->actingAs($user)
            ->put(route('contact_groups.update', [$contactGroup->slug]),
                array_merge($contactGroup2->toArray(),
                    ['_token' => csrf_token()]
                ));

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseHas('contact_groups', $contactGroup2->toArray());
        $this->assertDatabaseMissing('contact_groups', $check);
        $this->assertAuthenticatedAs($user);
    }
}
