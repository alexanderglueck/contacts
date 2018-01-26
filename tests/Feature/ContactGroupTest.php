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

        $response = $this
            ->actingAs($user)
            ->post(route('contact_groups.store'), $contactGroup->toArray());

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('contact_groups', $contactGroup->toArray());
        $this->assertAuthenticatedAs($user);
    }
}
