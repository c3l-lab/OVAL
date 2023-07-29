<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\Course;
use oval\Models\Group;
use oval\Models\User;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_unassigned(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $video = $course->defaultGroup()->videos()->first();
        $group = Group::factory()->create([
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($user)->get('/groups/unassigned?course_id=' . $course->id .'&video_id=' . $video->id);

        $response->dump();

        $response->assertStatus(200);
        $response->assertJsonPath('unassigned_groups.1.name', $group->name);
    }
}
