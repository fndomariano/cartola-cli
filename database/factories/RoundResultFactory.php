<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoundResult>
 */
class RoundResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'round' => 5,
            'score' => $faker->randomFloat(2),
            'ranking' => $faker->randomDigit()
            'team_id' => $this->faker->uuid,
        ];
    }
}
