<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use oval\Models\Course;
use oval\Models\User;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->createWithVideoForUser($user);
        $user->makeInstructorOf($course);

        $response = $this->actingAs($user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertSee($course->name);
    }
}
