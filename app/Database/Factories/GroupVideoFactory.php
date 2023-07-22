<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use oval\Models\GroupVideo;
use oval\Models\Group;
use oval\Models\Video;
use oval\Models\User;

class GroupVideoFactory extends Factory
{
    protected $model = GroupVideo::class;

    public function definition()
    {
        $group = Group::factory()->create();
        $video = Video::factory()->create();

        return [
            'group_id' => $group->id,
            'video_id' => $video->id,
            'hide' => $this->faker->boolean,
            'show_analysis' => $this->faker->boolean,
            'moodle_resource_id' => $this->faker->numberBetween(1, 100),
            'status' => 'current',
            'order' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
