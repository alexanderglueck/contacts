<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\Team;
use App\Models\User;
use App\Models\Admin\Announcement;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_an_announcement()
    {
        $user = $this->createUser('create announcements');

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $parameters = $announcement->toArray();

        $response = $this
            ->post(route('announcements.store'), $parameters);

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertModelExists( $announcement);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_an_announcement_created_by_him()
    {
        $user = $this->createUser('view announcements');

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $response = $this
            ->get(route('announcements.show', [$announcement->ulid]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_the_announcement_delete_view()
    {
        $user = $this->createUser('delete announcements');

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $response = $this
            ->get(route('announcements.delete', [$announcement->ulid]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_delete_an_announcement()
    {
        $user = $this->createUser('delete announcements');

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $this->assertModelExists( $announcement);

        $response = $this
            ->delete(route('announcements.destroy', [$announcement->ulid]));

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseMissing('announcements', $announcement->toArray());
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_view_the_announcement_edit_view()
    {
        $user = $this->createUser('edit announcements');

        $this->be($user);

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('announcements.edit', [$announcement->ulid]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Announcements/Edit'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_user_can_update_an_announcement()
    {
        Session::start();

        $user = $this->createUser('edit announcements');

        $this->be($user);

        $announcement = create(Announcement::class, [
            'user_id' => $user->id
        ]);

        $check = [
            'id' => $announcement->id,
            'title' => $announcement->title,
        ];

        $differentAnnouncement = $announcement;
        $differentAnnouncement->title = "something else";

        $parameters = $differentAnnouncement->toArray();

        unset($parameters['updated_at']);
        unset($differentAnnouncement['updated_at']);
        unset($parameters['updated_by']);
        unset($differentAnnouncement['updated_by']);

        $response = $this->actingAs($user)
            ->put(route('announcements.update', [$announcement->ulid]),
                array_merge($parameters,
                    ['_token' => csrf_token()]
                ));

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertModelExists( $differentAnnouncement);
        $this->assertDatabaseMissing('announcements', $check);
        $this->assertAuthenticatedAs($user);
    }
}
