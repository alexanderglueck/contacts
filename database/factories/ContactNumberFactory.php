<?php

namespace Database\Factories;

use App\Models\ContactNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactNumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactNumber::class;

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
            'name' => $this->faker->word,
            'number' => $this->faker->phoneNumber,
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
