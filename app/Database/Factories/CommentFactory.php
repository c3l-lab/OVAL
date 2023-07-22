<?php

namespace Database\Factories;

use oval\Models\Comment;
use oval\Models\User;
use oval\Models\GroupVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        $groupVideo = GroupVideo::inRandomOrder()->first();
        if (!$groupVideo) {
            $groupVideo = GroupVideo::factory()->create();
        }

        return [
            'group_video_id' => $groupVideo->id,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'description' => $this->faker->sentence,
            'privacy' => $this->faker->randomElement(['private', 'all', 'nominated']),
            'visible_to' => $this->faker->optional()->randomElements(range(1, 10), $count = $this->faker->numberBetween($min = 1, $max = 10)),
            'status' => 'current',
            'parent' => null,
        ];
    }
}
