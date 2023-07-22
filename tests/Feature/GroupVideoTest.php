<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\User;
use Tests\TestCase;

class GroupVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_video(): void
    {
        $course = Course::factory()->has(Group::factory()->count(1))->create();
        User::factory()->create();

        $youtubeId = "dQw4w9WgXcQ";

        $response = $this->post('/add_video', [
                "api_token" => "test_token",
                "video_id" => $youtubeId,
                "media_type" => "youtube",
                "course_id" => $course->id,
                "request_analysis" => false
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('videos', [
            'identifier' => $youtubeId,
            'media_type' => 'youtube',
        ]);
    }
}
