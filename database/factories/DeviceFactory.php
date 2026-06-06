<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word.' Phone',
            'device_token' => $this->faker->sha256(),
            'user_id' => User::factory(),
        ];
    }

    public function withoutToken(): self
    {
        return $this->state(fn () => ['device_token' => null]);
    }
}
