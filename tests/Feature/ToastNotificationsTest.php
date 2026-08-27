<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AiProviderSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ToastNotificationsTest extends TestCase
{
    use RefreshDatabase;
    public function test_successful_autopilot_save_sets_flash()
    {
        $user = User::factory()->create();

        $data = [
            'enabled' => 1,
            'mode' => 'manual',
            'posts_per_day' => 3,
            'timezone' => 'Asia/Jakarta',
        ];

        $response = $this->actingAs($user)->put(route('settings.update'), $data);

        $response->assertRedirect(route('settings'));
        $response->assertSessionHas('success');
    }

    public function test_ai_test_without_api_key_returns_configuration_required()
    {
        $user = User::factory()->create();

        $payload = ['provider' => 'openai', 'text_model' => 'gpt-4'];

        $response = $this->actingAs($user)->postJson(route('settings.ai.test'), $payload);

        $response->assertStatus(422);
        $response->assertJson(['status' => 'configuration_required']);
    }

    public function test_ai_test_success_with_mock_provider_returns_success()
    {
        $user = User::factory()->create();

        // store encrypted API key for user
        $rec = AiProviderSetting::create([
            'user_id' => $user->id,
            'provider' => 'openai',
            'text_model' => 'gpt-4',
            'api_key_encrypted' => Crypt::encryptString('sk-test-123'),
        ]);

        // fake provider HTTP
        Http::fake(function ($request) {
            return Http::response(['id' => 'resp', 'choices' => []], 200);
        });

        $payload = ['provider' => 'openai', 'text_model' => 'gpt-4'];

        $response = $this->actingAs($user)->postJson(route('settings.ai.test'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertStringNotContainsString('sk-test-123', $response->getContent());
    }
}
