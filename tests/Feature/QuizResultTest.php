<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\QuizCreation;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class QuizResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_store(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->post("/quiz_results", [
            "user_id" => $user->id,
            "group_video_id" => $groupVideo->id,
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
                            "feedback" => [""],
                            "user_ans" => "A:4342",
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_result', [
            'group_video_id' => $groupVideo->id,
        ]);
    }
}
