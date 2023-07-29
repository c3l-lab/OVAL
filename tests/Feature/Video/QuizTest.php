<?php

namespace Tests\Feature\Video;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\Course;
use oval\Models\QuizCreation;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_show(): void
    {
        $user = User::factory()->create();
        $video = Video::factory()->create();
        QuizCreation::factory()->create([
            'identifier' => $video->identifier,
        ]);

        $response = $this->actingAs($user)->get("/videos/{$video->identifier}/quiz");

        $response->assertStatus(200);
        $response->assertJsonIsObject();
        $response->assertJsonPath('quiz.identifier', $video->identifier);
    }

    public function test_create(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $video = $course->defaultGroup()->videos->first();

        $response = $this->actingAs($user)->put("/videos/{$video->identifier}/quiz", [
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
            'identifier' => $video->identifier,
        ]);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $video = $course->defaultGroup()->videos->first();
        QuizCreation::factory()->create([
            'identifier' => $video->identifier,
        ]);

        $response = $this->actingAs($user)->put("/videos/{$video->identifier}/quiz", [
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
            'identifier' => $video->identifier,
        ]);
    }
}
