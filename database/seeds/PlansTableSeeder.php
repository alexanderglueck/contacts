<?php

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Monthly',
                'slug' => 'monthly',
                'gateway_id' => 'plan_CsD4Imuq20ILlS',
                'price' => 600,
                'active' => true,
                'teams_enabled' => false,
                'teams_limit' => null
            ],
            [
                'name' => 'Yearly',
                'slug' => 'yearly',
                'gateway_id' => 'year_60',
                'price' => 6000,
                'active' => true,
                'teams_enabled' => false,
                'teams_limit' => null
            ],
            [
                'name' => 'Monthly for 10 users',
                'slug' => 'monthly-for-10-users',
                'gateway_id' => 'team_month_10',
                'price' => 5500,
                'active' => true,
                'teams_enabled' => true,
                'teams_limit' => 10
            ],
        ];

        Plan::insert($plans);
    }
}
