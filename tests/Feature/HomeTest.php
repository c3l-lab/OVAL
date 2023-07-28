<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\Course;
use oval\Models\GroupVideo;
use oval\Models\User;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_without_video(): void
    {
        GroupVideo::all()->map(function ($groupVideo) {
            $groupVideo->hide = true;
            $groupVideo->save();
        });
        $user = User::find(10000001);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('There is no video for the ID you selected');
    }

    public function test_index_with_video(): void
    {
        $course = Course::first();
        $user = User::find(10000001);
        $user->addToGroup($course->defaultGroup());

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirectToRoute('group_videos.show', ['group_video' => $course->defaultGroup()->group_videos()->first()]);
    }

}
