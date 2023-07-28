<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\LtiConsumer>
 */
class LtiConsumerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name,
            "consumer_key256" => fake()->regexify('[a-zA-Z0-9]{60}'),
            "secret" =>  fake()->regexify('[a-zA-Z0-9]{60}'),
        ];
    }
}
