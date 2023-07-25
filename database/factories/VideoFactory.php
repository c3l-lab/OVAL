<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use oval\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "identifier" => fake()->unique()->regexify('[a-zA-Z0-9]{11}'),
            "title" => $this->faker->sentence,
            "description" => $this->faker->paragraph,
            "duration" => $this->faker->numberBetween(0, 1000),
            "thumbnail_url" => $this->faker->imageUrl(),
            "media_type" => "youtube",
            "added_by" => User::factory(),
        ];
    }
}
