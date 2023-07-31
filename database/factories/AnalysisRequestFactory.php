<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use oval\Models\User;
use oval\Models\Video;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\AnalysisRequest>
 */
class AnalysisRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'video_id' => Video::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'processed', 'rejected', 'deleted']),
        ];
    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => "pending",
            ];
        });
    }

    public function processing(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => "processing",
            ];
        });
    }

    public function processed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => "processed",
            ];
        });
    }

    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => "rejected",
            ];
        });
    }

    public function deleted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => "deleted",
            ];
        });
    }
}
