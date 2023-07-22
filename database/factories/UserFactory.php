<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use oval\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'api_token' => 'test_token',
        ];
    }

    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => "A",
            ];
        });
    }
}
