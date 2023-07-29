<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\User;
use oval\Models\Video;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\oval\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Test Course',
            'platform_course_id' => fake()->unique()->randomNumber(8),
        ];
    }

    public function createWithVideoForUser($user)
    {
        $course = Course::factory()->has(
            Group::factory()->count(1)
        )->create();
        $group = $course->defaultGroup();
        $video = Video::factory()->create();
        $video->assignToGroup($group);
        $user = User::factory()->create();
        $user->addToGroup($group);
        $user->makeInstructorOf($course);
        return $course;
    }
}
