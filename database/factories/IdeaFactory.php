<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => fake()->optional()->numberBetween(1, 5), // optional team_id between 1 and 5
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'links' => [fake()->url()],
        ];
    }
}
