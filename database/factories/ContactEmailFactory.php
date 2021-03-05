<?php

namespace Database\Factories;

use App\Models\ContactEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactEmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactEmail::class;

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
            'email' => $this->faker->email,
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
