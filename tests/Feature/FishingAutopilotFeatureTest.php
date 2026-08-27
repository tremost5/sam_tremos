<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FishingAutopilotFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Sam Tremos');
    }

    public function test_authenticated_user_can_view_posts_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/posts')->assertOk();
    }

    public function test_authenticated_user_can_view_ai_generation_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/ai/generate')->assertOk();
    }
}
