<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Squad>
 */
class SquadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->regexify('([A-Z][0-9]){8}'),
            'description' => $this->faker->optional()->text(rand(10,300)),
            'link' => $this->faker->optional()->url(),
            'country' => $this->faker->optional()->countryCode(),
        ];
    }
}
