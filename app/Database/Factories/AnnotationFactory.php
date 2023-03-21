<?php

namespace Database\Factories;

use oval\Annotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnotationFactory extends Factory
{
    protected $model = Annotation::class;

    public function definition()
    {
        return [
            'video_id' => function () {
                return Video::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'start_time' => $this->faker->randomNumber,
            'description' => $this->faker->sentence,
            'tags' => $this->faker->optional()->word,
            'is_private' => $this->faker->boolean($chanceOfGettingTrue = 20),
        ];
    }
}
