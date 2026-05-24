<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\ContactGroup;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class ContactGroupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_a_contact_group()
    {
        $user = $this->createUser('create contactGroups');

        $contactGroup = make(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertDatabaseMissing('contact_groups', $contactGroup->toArray());

        $response = $this
            ->post(route('contact_groups.store'), $contactGroup->toArray());

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('contact_groups', $contactGroup->toArray());
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_a_contact_group()
    {
        $user = $this->createUser('view contactGroups');

        $contactGroup = create(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $response = $this
            ->get(route('contact_groups.show', [$contactGroup->ulid]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_the_contact_group_delete_view()
    {
        $user = $this->createUser('delete contactGroups');

        $contactGroup = create(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $response = $this
            ->get(route('contact_groups.delete', [$contactGroup->ulid]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_delete_a_contact_group()
    {
        $user = $this->createUser('delete contactGroups');

        $contactGroup = create(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $this->assertModelExists($contactGroup);

        $response = $this
            ->delete(route('contact_groups.destroy', [$contactGroup->ulid]), [
                '_token' => csrf_token()
            ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertModelMissing( $contactGroup);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_the_contact_group_edit_view()
    {
        $user = $this->createUser('edit contactGroups');

        $contactGroup = create(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $response = $this
            ->get(route('contact_groups.edit', [$contactGroup->ulid]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('ContactGroups/Edit'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_update_a_contact_group()
    {
        $user = $this->createUser('edit contactGroups');

        $contactGroup = create(ContactGroup::class, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $check = $contactGroup->toArray();

        $contactGroup2 = $contactGroup;
        $contactGroup2->name = "something else";

        unset($contactGroup2->updated_at);
        unset($contactGroup2->updated_by);
        unset($contactGroup->updated_at);
        unset($contactGroup->updated_by);

        $response = $this
            ->put(route('contact_groups.update', [$contactGroup->ulid]),
                array_merge($contactGroup2->toArray(),
                    ['_token' => csrf_token()]
                ));

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertModelExists($contactGroup2);
        $this->assertDatabaseMissing('contact_groups', $check);
        $this->assertAuthenticatedAs($user);
    }
}
