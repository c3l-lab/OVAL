<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\Annotation>
 */
class AnnotationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "description" => fake()->text(100),
            "start_time" => fake()->numberBetween(0, 100),
            "privacy" => fake()->randomElement(['all', 'private', 'nominated']),
            "status" => 'current',
        ];
    }
}
