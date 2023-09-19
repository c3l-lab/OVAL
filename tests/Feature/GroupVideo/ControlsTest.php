<?php

namespace Tests\Feature\GroupVideo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\Course;
use oval\Models\QuizCreation;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class ControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_update(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->putJson('/group_videos/' . $groupVideo->id . '/controls', [
            "pause" => true,
            "fullscreen" => false,
        ]);

        $response->assertStatus(200);
    }
}
