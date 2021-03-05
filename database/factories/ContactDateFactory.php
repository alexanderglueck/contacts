<?php

namespace Database\Factories;

use App\Models\ContactDate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactDateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactDate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $contacts;

        $contacts = $contacts ?: \App\Models\Contact::all()->keys()->toArray();

        $skipYear = $this->faker->boolean;

        return [
            'contact_id' => $this->faker->randomElement($contacts),
            'name' => $this->faker->streetName,
            'date' => $skipYear ? $this->faker->date('d.m.1900') : $this->faker->date('d.m.Y'),
            'skip_year' => $skipYear,
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}

