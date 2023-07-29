<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $video = Video::factory()->create();
        QuizCreation::factory()->create([
            'identifier' => $video->identifier,
        ]);

        $response = $this->actingAs($user)->post("/quiz_results", [
            "user_id" => $user->id,
            "identifier" => $video->identifier,
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
            'identifier' => $video->identifier,
        ]);
    }
}
