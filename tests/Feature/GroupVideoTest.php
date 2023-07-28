<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\GroupVideo;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class GroupVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $user = User::factory()->create();
        $group = $course->defaultGroup();
        $user->addToGroup($group);
        $user->makeInstructorOf($course);
        $videos = $group->group_videos();

        $unassignedVideo = Video::factory()->create();

        $response = $this->actingAs($user)->get('/group_videos');
        $response->assertStatus(200);
        $response->assertSee($unassignedVideo->title);
        $response->assertSeeInOrder($videos->pluck('title')->toArray());
    }

    public function test_show_group_video(): void
    {
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $group = $course->defaultGroup();
        $user = User::factory()->create();
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
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $group = $course->defaultGroup();
        $user = User::factory()->create();
        $video = $course->defaultGroup()->videos()->first();
        $video->assignToGroup($group);
        $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->first();

        $response = $this->actingAs($user)->get('/group_videos/' . $group_video->id);
        $response->assertStatus(404);
    }

    public function test_destroy(): void
    {
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $group = $course->defaultGroup();
        $user = User::factory()->create();
        $user->addToGroup($group);
        $user->makeInstructorOf($course);
        $video = $course->defaultGroup()->videos()->first();
        $video->assignToGroup($group);
        $group_video = $group->group_videos()
                    ->where('status', '=', 'current')
                    ->first();

        $response = $this->actingAs($user)->delete('/group_videos/' . $group_video->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('group_videos', [
            'id' => $group_video->id,
        ]);
    }
}
