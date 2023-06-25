<?php

namespace Database\Factories;

use App\Models\RoundResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoundResult>
 */
class RoundResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RoundResult::class;

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
            'score' => $this->faker->randomFloat(2),
            'ranking' => $this->faker->randomDigit(),
            'team_id' => $this->faker->uuid,
        ];
    }
}
