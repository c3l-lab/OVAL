<?php

namespace Tests\Feature\GroupVideo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\QuizCreation;
use oval\Models\User;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_show(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        QuizCreation::factory()->create([
            'group_video_id' => $groupVideo->id,
        ]);

        $response = $this->actingAs($user)->get("/group_videos/{$groupVideo->id}/quiz");

        $response->assertStatus(200);
        $response->assertJsonIsObject();
        $response->assertJsonPath('quiz.group_video_id', $groupVideo->id);
    }

    public function test_create(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->put("/group_videos/{$groupVideo->id}/quiz", [
            "course_id" => $course->id,
            "media_type" => "youtube",
            "quiz_data" => [
                [
                    "name" => "Quiz @ 8 seconds",
                    "stop" => "8",
                    "items" => [
                        [
                            "ans" => ["A:4342"],
                            "list" => ["A:4342"],
                            "type" => "multiple_choice",
                            "title" => "test question title",
                            "feedback" => [""]
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_creation', [
            'group_video_id' => $groupVideo->id,
        ]);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->put("/group_videos/{$groupVideo->id}/quiz", [
            "course_id" => $course->id,
            "media_type" => "youtube",
            "quiz_data" => [
                [
                    "name" => "Quiz @ 8 seconds",
                    "stop" => "8",
                    "items" => [
                        [
                            "ans" => ["A:4342"],
                            "list" => ["A:4342"],
                            "type" => "multiple_choice",
                            "title" => "test question title",
                            "feedback" => [""]
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_creation', [
            'group_video_id' => $groupVideo->id,
        ]);
    }

    public function test_toggle_visible()
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->post("/group_videos/{$groupVideo->id}/quiz/toggle_visible", [
            "visable" => 1,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_creation', [
            'group_video_id' => $groupVideo->id,
            'visable' => 1,
        ]);

        $response = $this->actingAs($user)->post("/group_videos/{$groupVideo->id}/quiz/toggle_visible", [
            "visable" => 0,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_creation', [
            'group_video_id' => $groupVideo->id,
            'visable' => 0,
        ]);
        $this->assertDatabaseCount('quiz_creation', 1);
    }
}
