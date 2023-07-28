<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\User;
use Tests\TestCase;

class GroupVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_group_video(): void
    {
        $course = Course::first();
        $group = $course->defaultGroup();
        $user = User::find(10000001);
        $user->addToGroup($group);
        $user->makeInstructorOf($course);
        $video = $course->defaultGroup()->videos()->first();
        $video->assignToGroup($group);
        $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->first();

        $response = $this->actingAs($user)->get('/group_videos/' . $group_video->id);
        $response->assertStatus(200);
        $response->assertSee($course->name);
    }

    public function test_show_group_video_without_permission(): void
    {
        $course = Course::first();
        $group = $course->defaultGroup();
        $user = User::find(10000001);
        $video = $course->defaultGroup()->videos()->first();
        $video->assignToGroup($group);
        $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->first();

        $response = $this->actingAs($user)->get('/group_videos/' . $group_video->id);
        $response->assertStatus(404);
    }
}
