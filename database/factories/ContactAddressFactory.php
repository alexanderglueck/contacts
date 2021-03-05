<?php

namespace Database\Factories;

use App\Models\ContactAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $contacts;

        $contacts = $contacts ?: \App\Models\Contact::all()->keys()->toArray();

        return [
            'contact_id' => $this->faker->randomElement($contacts),
            'name' => $this->faker->streetName,
            'street' => $this->faker->streetAddress,
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'state' => '',
            'country_id' => 164,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
