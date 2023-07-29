<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use oval\Models\User;
use Tests\TestCase;

class TranscriptTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/transcripts');

        $response->assertStatus(200);
    }

    public function test_store(): void
    {

    }

    public function test_upload(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/transcripts/upload', [
            'file' => UploadedFile::fake()->createWithContent('transcript.srt', file_get_contents(__DIR__ . '/../fixtures/transcripts.json')),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('videos', [
            'identifier' => 'AounoSj7QuQ',
        ]);
        $this->assertDatabaseHas('transcripts', [
            'video_id' => 1,
        ]);
    }

}
