<?php

namespace Tests\Feature;

use Google\Service\AndroidPublisher\VoidedPurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Annotation;
use oval\Models\Course;
use oval\Models\Tag;
use oval\Models\User;
use Tests\TestCase;

class AnnotationTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $annotation = Annotation::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/annotations?course_id=' . $course->id . '&group_id=' . $groupVideo->group_id . '&video_id=' . $groupVideo->video_id);

        $response->assertStatus(200);
        $response->assertJsonPath('0.id', $annotation->id);
    }

    public function test_tag(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $annotation = Annotation::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);
        $tag = Tag::factory()->create();
        $annotation->tags()->attach($tag);

        $response = $this->actingAs($user)->get('/annotations/tag?tag=' . $tag->tag . '&group_video_id=' . $groupVideo->id);

        $response->assertStatus(200);
        $response->assertJsonPath('0.id', $annotation->id);
    }


    public function test_store(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();

        $response = $this->actingAs($user)->post('/annotations', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test annotation',
            'start_time' => 3,
            'privacy' => 'all',
            'tags' => ["test"],
            'nominated_students_ids' => null,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('annotations', [
            'group_video_id' => $groupVideo->id,
            'description' => 'test annotation',
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
        $annotation = Annotation::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put('/annotations/' . $annotation->id, [
            'description' => 'test annotation',
            'start_time' => 3,
            'privacy' => 'all',
            'tags' => ["test"],
            'nominated_students_ids' => null,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('annotations', [
            'id' => $annotation->id,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('annotations', [
            'description' => 'test annotation',
            'privacy' => 'all',
        ]);
    }


    public function test_destory(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $groupVideo = $course->defaultGroup()->group_videos()->first();
        $annotation = Annotation::factory()->create([
            'group_video_id' => $groupVideo->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete('/annotations/' . $annotation->id);

        $response->assertStatus(200);

        $this->assertDatabaseHas('annotations', [
            'id' => $annotation->id,
            'status' => 'deleted',
        ]);
    }
}
