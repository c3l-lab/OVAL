<?php

namespace Tests\Feature\Video;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\User;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $group = $course->defaultGroup();
        $video = $course->defaultGroup()->videos()->first();

        $response = $this->actingAs($user)->get(route('videos.groups.index', ['video' => $video]));

        $response->assertStatus(200);
        $response->assertJsonPath('groups.0.id', $group->id);
    }
}
