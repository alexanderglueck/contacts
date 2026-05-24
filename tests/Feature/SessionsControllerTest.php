<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SessionsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_index_lists_only_the_current_users_session_rows()
    {
        $owner = $this->createUser();
        $sessionId = session()->getId();

        // The signed-in user's row, plus a stranger's row that should not leak.
        DB::table('sessions')->insert([
            ['id' => $sessionId, 'user_id' => $owner->id, 'ip_address' => '203.0.113.1', 'user_agent' => 'Mozilla/5.0', 'payload' => '', 'last_activity' => now()->timestamp],
            ['id' => 'stranger', 'user_id' => $owner->id + 999, 'ip_address' => '198.51.100.1', 'user_agent' => 'Mozilla/5.0', 'payload' => '', 'last_activity' => now()->timestamp],
        ]);

        $response = $this->get(route('user_settings.sessions.index'));

        $response->assertStatus(200);
        $response->assertDontSee('stranger');
    }

    #[Test]
    public function destroying_someone_elses_session_row_does_nothing()
    {
        Config::set('session.driver', 'database');

        $owner = $this->createUser();
        $sessionId = session()->getId();

        DB::table('sessions')->insert([
            ['id' => $sessionId, 'user_id' => $owner->id, 'ip_address' => '203.0.113.1', 'user_agent' => 'Mozilla/5.0', 'payload' => '', 'last_activity' => now()->timestamp],
            ['id' => 'someone-else', 'user_id' => $owner->id + 999, 'ip_address' => '198.51.100.1', 'user_agent' => 'Mozilla/5.0', 'payload' => '', 'last_activity' => now()->timestamp],
        ]);

        $this->delete(route('user_settings.sessions.destroy', 'someone-else'));

        // The destroy query is scoped by user_id, so a stranger's row
        // survives no matter what ID the caller passes.
        $this->assertDatabaseHas('sessions', ['id' => 'someone-else']);
    }

    #[Test]
    public function destroying_the_current_session_id_is_refused_via_the_per_row_endpoint()
    {
        Config::set('session.driver', 'database');

        $owner = $this->createUser();
        $sessionId = session()->getId();

        DB::table('sessions')->insert([
            ['id' => $sessionId, 'user_id' => $owner->id, 'ip_address' => '203.0.113.1', 'user_agent' => 'Mozilla/5.0', 'payload' => '', 'last_activity' => now()->timestamp],
        ]);

        $this->delete(route('user_settings.sessions.destroy', $sessionId));

        // The current row stays — destroying yourself goes through the
        // bulk "log out other browsers" flow instead.
        $this->assertDatabaseHas('sessions', ['id' => $sessionId]);
    }
}
