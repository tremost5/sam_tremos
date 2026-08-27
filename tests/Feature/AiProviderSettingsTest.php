<?php

namespace Tests\Feature;

use App\Models\AiProviderSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AiProviderSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_ai_settings()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('AI Provider Configuration');
    }

    public function test_user_can_save_api_key_encrypted()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put('/settings/ai', [
            'provider' => 'openai',
            'text_model' => 'gpt-test',
            'api_key' => 'super-secret-key',
        ]);

        $this->assertDatabaseHas('ai_provider_settings', ['user_id' => $user->id, 'provider' => 'openai']);
        $row = AiProviderSetting::where('user_id', $user->id)->first();
        $this->assertNotNull($row->api_key_encrypted);
        $this->assertEquals('super-secret-key', Crypt::decryptString($row->api_key_encrypted));
    }

    public function test_api_key_not_shown_in_html_or_json()
    {
        $user = User::factory()->create();
        AiProviderSetting::create(['user_id' => $user->id, 'provider' => 'openai', 'text_model' => 'gpt-test', 'api_key_encrypted' => Crypt::encryptString('secret')]);

        $response = $this->actingAs($user)->get('/settings');
        $content = $response->getContent();

        $this->assertStringNotContainsString('secret', $content);

        $responseJson = $this->actingAs($user)->getJson('/settings');
        $responseJson->assertStatus(200);
        $this->assertStringNotContainsString('secret', $responseJson->getContent());
    }

    public function test_update_without_api_key_keeps_existing()
    {
        $user = User::factory()->create();
        AiProviderSetting::create(['user_id' => $user->id, 'provider' => 'openai', 'text_model' => 'gpt-test', 'api_key_encrypted' => Crypt::encryptString('orig')]);

        $this->actingAs($user)->put('/settings/ai', ['provider' => 'openai', 'text_model' => 'gpt-new']);

        $row = AiProviderSetting::where('user_id', $user->id)->first();
        $this->assertEquals('orig', Crypt::decryptString($row->api_key_encrypted));
        $this->assertEquals('gpt-new', $row->text_model);
    }

    public function test_user_a_cant_read_user_b_settings()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        AiProviderSetting::create(['user_id' => $userB->id, 'provider' => 'openai', 'text_model' => 'gpt-test', 'api_key_encrypted' => Crypt::encryptString('secret-b')]);

        $this->actingAs($userA)->get('/settings');
        $this->assertDatabaseHas('ai_provider_settings', ['user_id' => $userB->id]);
        // Ensure page for userA does not show userB secret
        $response = $this->actingAs($userA)->get('/settings');
        $this->assertStringNotContainsString('secret-b', $response->getContent());
    }
}
