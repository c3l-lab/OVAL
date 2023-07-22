<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\User;
use oval\Models\Video;
use oval\Models\Comment;

class AjaxTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsMinnieMouse() {
        // Create a user for testing purposes
        $minnie = oval\Models\User::factory()->create();
        $this->be($minnie);
    }

    public function testGetComments() {
        $this->loginAsMinnieMouse();

        // Create a video
        $video = Video::factory()->create();

        // Create some comments
        Comment::factory()->count(3)->create(['video_id' => $video->id]);

        $this->json('GET', '/get_comments', ['video_id' => $video->id])
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'video_id',
                    'content',
                    'author',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

}
