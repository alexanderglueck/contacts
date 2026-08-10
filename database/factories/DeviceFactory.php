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
            'fid' => null,
            'user_id' => User::factory(),
        ];
    }

    public function withoutToken(): self
    {
        return $this->state(fn () => ['device_token' => null]);
    }

    /**
     * A device registered by a current app build, which identifies itself by
     * Firebase Installation ID.
     */
    public function withFid(?string $fid = null): self
    {
        return $this->state(fn () => ['fid' => $fid ?? $this->faker->regexify('[A-Za-z0-9_-]{22}')]);
    }
}
