<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\GroupVideo;
use oval\Models\User;
use oval\Models\Video;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_without_video(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('There is no video for the ID you selected');
    }

    public function test_index_with_video(): void
    {
        $course = Course::factory()->has(
            Group::factory()->has(
                Video::factory()->count(1)
            )->count(1)
        )->create();
        $user = User::factory()->create();

        $user->addToGroup($course->defaultGroup());

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirectToRoute('group_videos.show', ['group_video' => $course->defaultGroup()->group_videos()->first()]);
    }

}
