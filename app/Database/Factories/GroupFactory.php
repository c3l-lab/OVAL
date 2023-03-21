<?php

namespace Database\Factories;

use oval\Group;
use oval\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition()
    {
        $course = Course::factory()->create();

        return [
            'name' => $this->faker->word,
            'course_id' => $course->id,
        ];
    }
}
