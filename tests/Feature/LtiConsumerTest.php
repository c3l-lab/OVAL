<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use oval\Models\LtiConsumer;
use oval\Models\User;
use Tests\TestCase;

class LtiConsumerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->admin()->create();
        $consumers = LtiConsumer::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/lti/consumers');

        $response->assertStatus(200);
        $response->assertSeeInOrder($consumers->pluck('name')->toArray());
    }

    public function test_store(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/lti/consumers', [
            "name" => "Test Consumer",
            "key" => "test_key",
            "secret" => "test_secret",
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas("lti2_consumer", [
            "name" => "Test Consumer",
        ]);
    }

    public function test_show(): void
    {
        $user = User::factory()->admin()->create();
        $consumer = LtiConsumer::factory()->create();

        $response = $this->actingAs($user)->get('/lti/consumers/' . $consumer->consumer_pk);

        $response->assertStatus(200);
        $response->assertSee($consumer->name);
    }

    public function test_update(): void
    {
        $user = User::factory()->admin()->create();
        $consumer = LtiConsumer::factory()->create();

        $response = $this->actingAs($user)->put('/lti/consumers/' . $consumer->consumer_pk, [
            "name" => "Test Consumer",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("lti2_consumer", [
            "name" => "Test Consumer",
        ]);
    }

    public function test_destory(): void
    {
        $user = User::factory()->admin()->create();
        $consumer = LtiConsumer::factory()->create();

        $response = $this->actingAs($user)->delete('/lti/consumers/' . $consumer->consumer_pk);

        $response->assertStatus(200);
        $response->assertJson([
            "result" => true,
        ]);
    }
}
