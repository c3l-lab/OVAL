<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use oval\Models\Course;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_video(): void
    {
        $course = Course::first();
        $user = User::find(10000001);

        $youtubeId = "dQw4w9WgXcQ";

        $response = $this->actingAs($user)->post('/videos', [
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

    public function test_destory_video(): void
    {
        $video = Video::factory()->create();
        $user = User::find(10000001);

        $response = $this->actingAs($user)->delete(route('videos.destroy', $video->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('videos', [
            'id' => $video->id
        ]);
    }
}
