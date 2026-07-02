<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'idea_id' => Idea::factory(), // we want to create a new idea for each step, so we use the Idea factory to generate a new idea and associate it with the step
            'description' => fake()->sentence(),
            'completed' => false,
        ];
    }
}
