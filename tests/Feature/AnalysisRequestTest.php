<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\AnalysisRequest;
use oval\Models\Course;
use oval\Models\User;
use Tests\TestCase;

class AnalysisRequestTest extends TestCase
{
    use RefreshDatabase;
    public function test_index(): void
    {
        $user = User::factory()->admin()->create();

        $pending = AnalysisRequest::factory()->pending()->create();
        $rejected = AnalysisRequest::factory()->rejected()->create();
        $processed = AnalysisRequest::factory()->processed()->create();

        $response = $this->actingAs($user)->get('/analysis_requests');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $pending->video->title,
            $rejected->video->title,
            $processed->video->title,
        ]);
    }

    public function test_store(): void
    {
        $this->markTestIncomplete("TODO: implement this test");
    }

    public function test_resend(): void
    {
        $user = User::factory()->admin()->create();

        $rejected = AnalysisRequest::factory()->rejected()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/' . $rejected->id . '/resend');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $rejected->id,
            'status' => 'processed',
        ]);
    }

    public function test_reject(): void
    {
        $user = User::factory()->admin()->create();

        $pending = AnalysisRequest::factory()->pending()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/' . $pending->id . '/reject');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $pending->id,
            'status' => 'rejected',
        ]);
    }

    public function test_recover(): void
    {
        $user = User::factory()->admin()->create();

        $rejected = AnalysisRequest::factory()->rejected()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/' . $rejected->id . '/recover');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $rejected->id,
            'status' => 'pending',
        ]);
    }

    public function test_destory(): void
    {
        $user = User::factory()->admin()->create();

        $rejected = AnalysisRequest::factory()->rejected()->create();

        $response = $this->actingAs($user)->delete('/analysis_requests/' . $rejected->id . '');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $rejected->id,
            'status' => 'deleted',
        ]);
    }

    public function test_batch_resend(): void
    {
        $user = User::factory()->admin()->create();

        $pending = AnalysisRequest::factory()->pending()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/batch_resend');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $pending->id,
            'status' => 'processed',
        ]);
    }

    public function test_batch_reject(): void
    {
        $user = User::factory()->admin()->create();

        $pending = AnalysisRequest::factory()->pending()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/batch_reject');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $pending->id,
            'status' => 'rejected',
        ]);
    }

    public function test_batch_recover(): void
    {
        $user = User::factory()->admin()->create();

        $rejected = AnalysisRequest::factory()->rejected()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/batch_recover');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $rejected->id,
            'status' => 'pending',
        ]);
    }

    public function test_batch_deleted(): void
    {
        $user = User::factory()->admin()->create();

        $rejected = AnalysisRequest::factory()->rejected()->create();

        $response = $this->actingAs($user)->post('/analysis_requests/batch_delete');

        $response->assertStatus(302);
        $this->assertDatabaseHas('analysis_requests', [
            'id' => $rejected->id,
            'status' => 'deleted',
        ]);
    }
}
