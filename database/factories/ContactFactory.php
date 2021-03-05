<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'title' => $this->faker->title,
            'title_after' => $this->faker->title,
            'date_of_birth' => $this->faker->date('d.m.Y'),
            'iban' => $this->faker->iban('AT'),
            'salutation' => $this->faker->word,
            'gender_id' => $this->faker->numberBetween(1, 2),
            // 'is_company' => $this->faker->boolean() ? 1 : 0,
            // 'vatin' => $this->faker->bankAccountNumber,
            'company' => $this->faker->company,
            'department' => $this->faker->jobTitle,
            'job' => $this->faker->jobTitle,
            'custom_id' => $this->faker->unique()->randomNumber(),
            'nickname' => $this->faker->userName,
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
