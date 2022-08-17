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
            'name' => $this->faker->unique()->userName(),
            'code' => $this->faker->unique()->regexify('([A-Z][0-9]){8}'),
            'country' => $this->faker->optional()->countryCode(),
            'requires_approval' => $this->faker->boolean(),
            'rank' => $this->faker->optional()->randomKey(config('unite.squad_ranks')),
            'active_members' => $this->faker->numberBetween(1, 30),
            'description' => $this->faker->optional()->text(rand(10,300)),
            'link' => $this->faker->optional()->url(),
        ];
    }
}
