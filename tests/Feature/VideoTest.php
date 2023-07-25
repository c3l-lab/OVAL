<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_video_page_without_video(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/view');

        $response->assertStatus(200);
        $response->assertSee('There is no video for the ID you selected');
    }

    public function test_default_video_page_with_video(): void
    {
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $user = User::factory()->create();
        $user->addToGroup($course->defaultGroup());

        $response = $this->actingAs($user)->get('/view');

        $response->assertStatus(200);
        $response->assertSee($course->name);
    }

    public function test_creating_video(): void
    {
        $course = Course::factory()->has(Group::factory()->count(1))->create();
        $user = User::factory()->create();

        $youtubeId = "dQw4w9WgXcQ";

        $response = $this->post('/add_video', [
            "api_token" => $user->api_token,
            "video_id" => $youtubeId,
            "media_type" => "youtube",
            "course_id" => $course->id,
            "request_analysis" => false
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('course_id')
                ->has('video_id')
        );

        $this->assertDatabaseHas('videos', [
            'identifier' => $youtubeId,
            'title' => 'Rick Astley - Never Gonna Give You Up (Official Music Video)',
            'duration' => 213,
            'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/1.jpg',
            'media_type' => 'youtube',
        ]);
    }
}
