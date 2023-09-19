<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery\MockInterface;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\User;
use oval\Models\Video;
use oval\Services\YoutubeService;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_store(): void
    {
        $this->mock(YoutubeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchContentDetails')->andReturn(json_decode(json_encode([
                "items" => [[
                    "snippet" => [
                        "title" => "Rick Astley - Never Gonna Give You Up (Official Music Video)",
                        "description" => "foo",
                    ],
                    "contentDetails" => [
                        "duration" => "PT3M33S"
                    ]
                ]]
            ])));
        });

        $course = Course::factory()->has(Group::factory()->count(1))->create();
        $user = User::factory()->create();

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

    public function test_destory(): void
    {
        $video = Video::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('videos.destroy', $video->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('videos', [
            'id' => $video->id
        ]);
    }

    public function test_assign(): void
    {
        $user = User::factory()->create();
        $course1 = Course::factory()->createWithVideoForUser($user);
        $course2 = Course::factory()->createWithVideoForUser($user);
        $group1 = $course1->defaultGroup();
        $group2 = $course2->defaultGroup();
        $video = $group1->videos()->first();

        $response = $this->actingAs($user)->post('/videos/' . $video->id . '/assign', [
            "group_ids" => [$group2->id],
            "copy_from" => $group1->id,
            "copy_comment_instruction" => true,
            "copy_points" => true,
            "copy_quiz" => true,
        ]);

        $response->assertStatus(200);
        $this->assertSame($group2->videos()->count(), 2);
    }

    public function test_show(): void
    {
        $video = Video::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/videos/' . $video->id);

        $response->assertStatus(200);
        $response->assertJsonPath('id', $video->id);
    }
}
