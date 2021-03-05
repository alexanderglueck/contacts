<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;


class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'main',
            'stripe_id' => $this->faker->word,
            'stripe_plan' => $this->faker->word,
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null
        ];
    }
}
