<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrFail();

        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'Test'
        ]);

        $user->attachTeam($team);
    }
}
