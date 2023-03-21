<?php

namespace Database\Factories;

use oval\Video;
use oval\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition()
    {
        return [
            'identifier' => Str::random(10),
            'title' => $this->faker->optional()->sentence,
            'description' => $this->faker->optional()->sentence,
            'duration' => $this->faker->randomNumber,
            'thumbnail_url' => $this->faker->url,
            'media_type' => 'helix',
            'added_by' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
