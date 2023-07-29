<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use oval\Models\Comment;
use oval\Models\Tag;
use oval\Models\User;
use oval\Models\Course;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $comment = Comment::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/comments?group_video_id=' . $groupVideo->id);

        $response->assertStatus(200);
        $response->assertJsonPath('0.id', $comment->id);
    }

    public function test_tag(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $comment = Comment::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);
        $tag = Tag::factory()->create();
        $comment->tags()->attach($tag);

        $response = $this->actingAs($user)->get('/comments/tag?tag=' . $tag->tag . '&group_video_id=' . $groupVideo->id);

        $response->assertStatus(200);
        $response->assertJsonPath('0.id', $comment->id);
    }

    public function test_store(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->post('/comments', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test comment',
            'privacy' => 'all',
            'tags' => ["test"],
            'nominated_students_ids' => null,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test comment',
            'privacy' => 'all',
        ]);
        $this->assertDatabaseHas('tags', [
            'tag' => 'test',
        ]);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $comment = Comment::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put('/comments/' . $comment->id, [
            'description' => 'test comment',
            'privacy' => 'all',
            'tags' => ["test"],
            'nominated_students_ids' => null,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('comments', [
            'description' => 'test comment',
            'privacy' => 'all',
        ]);
    }

    public function test_destory(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $comment = Comment::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete('/comments/' . $comment->id);

        $response->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'deleted',
        ]);
    }
}
