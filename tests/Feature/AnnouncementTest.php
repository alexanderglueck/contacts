<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\Admin\Announcement;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnnouncementTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_can_create_an_announcement()
    {
        \Session::start();

        $this->withoutMiddleware();

        $user = $this->createUser('create announcements');

        $announcement = factory(Announcement::class)->make([
            'user_id' => $user->id
        ]);

        $parameters = $announcement->toArray();

        $response = $this
            ->actingAs($user)
            ->post(route('announcements.store'), $parameters);

        $response->assertStatus(302);
        $response->assertSessionMissing('errors');
        $this->assertDatabaseHas('announcements', $announcement->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_an_announcement_created_by_him()
    {
        $user = $this->createUser('view announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('announcements.show', [$announcement->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_not_view_an_announcement_created_by_another_team()
    {
        $user = $this->createUser('view announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
            'user_id' => $user->id
        ]);

        $anotherTeam = factory(Team::class)->create();
        $viewer = factory(User::class)->create([
            'current_team_id' => $anotherTeam->id
        ]);

        $response = $this->actingAs($viewer)
            ->get(route('announcements.show', [$announcement->slug]));

        $response->assertStatus(404);
        $this->assertAuthenticatedAs($viewer);

        $response = $this->actingAs($user)
            ->get(route('announcements.show', [$announcement->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_announcement_delete_view()
    {
        $user = $this->createUser('delete announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('announcements.delete', [$announcement->slug]));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_delete_an_announcement()
    {
        \Session::start();

        $user = $this->createUser('delete announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('announcements', $announcement->toArray());

        $response = $this->actingAs($user)
            ->delete(route('announcements.destroy', [$announcement->slug]), [
                '_token' => csrf_token()
            ]);

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseMissing('announcements', $announcement->toArray());
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_view_the_announcement_edit_view()
    {
        $user = $this->createUser('edit announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('announcements.edit', [$announcement->slug]));

        $response->assertStatus(200);
        $response->assertSee(trans('ui.edit_announcement'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function a_user_can_update_an_announcement()
    {
        \Session::start();

        $user = $this->createUser('edit announcements');

        $this->be($user);

        $announcement = factory(Announcement::class)->create([
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
            ->put(route('announcements.update', [$announcement->slug]),
                array_merge($parameters,
                    ['_token' => csrf_token()]
                ));

        $response->assertStatus(302);
        $response->assertSessionMissing('alert-danger');
        $this->assertDatabaseHas('announcements', $differentAnnouncement->toArray());
        $this->assertDatabaseMissing('announcements', $check);
        $this->assertAuthenticatedAs($user);
    }
}
