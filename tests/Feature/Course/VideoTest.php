<?php

namespace Tests\Feature\Course;

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

    public function test_index(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $video = $course->defaultGroup()->videos()->first();

        $response = $this->actingAs($user)->get('/courses/' . $course->id . '/videos');

        $response->assertStatus(200);
        $response->assertJsonPath('videos.0.id', $video->id);
    }
}
