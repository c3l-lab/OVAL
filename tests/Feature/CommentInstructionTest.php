<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\CommentInstruction;
use oval\Models\User;
use oval\Models\Course;
use Tests\TestCase;

class CommentInstructionTest extends TestCase
{
    use RefreshDatabase;

    public function test_store(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->post('/comment_instructions', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test description'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comment_instructions', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test description'
        ]);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $commentInstruction = CommentInstruction::factory()->create([
            'group_video_id' => $groupVideo->id,
        ]);

        $response = $this->actingAs($user)->post('/comment_instructions', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test description'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comment_instructions', [
            'id' => $commentInstruction->id,
            'description' => 'test description'
        ]);
    }

    public function test_destory(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $commentInstruction = CommentInstruction::factory()->create([
            'group_video_id' => $groupVideo->id,
        ]);

        $response = $this->actingAs($user)->delete('/comment_instructions/' . $groupVideo->id);

        $response->assertStatus(200);

        $this->assertModelMissing($commentInstruction);
    }
}
