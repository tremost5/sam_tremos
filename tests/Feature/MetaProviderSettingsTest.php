<?php

namespace Tests\Feature;

use App\Models\MetaProviderSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaProviderSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_meta_config_and_secret_encrypted()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('settings.meta.update'), [
            'app_id' => '12345',
            'app_secret' => 'super-secret',
            'redirect_uri' => route('facebook.callback'),
        ]);

        $this->assertDatabaseHas('meta_provider_settings', ['user_id' => $user->id, 'app_id' => '12345']);
        $row = MetaProviderSetting::where('user_id', $user->id)->first();
        $this->assertNotNull($row->app_secret_encrypted);
        $this->assertEquals('super-secret', Crypt::decryptString($row->app_secret_encrypted));
    }

    public function test_meta_secret_not_shown_in_html()
    {
        $user = User::factory()->create();
        $unique = 'SENSITIVE-SECRET-XYZ-123';
        MetaProviderSetting::create(['user_id' => $user->id, 'app_id' => '123', 'app_secret_encrypted' => Crypt::encryptString($unique), 'redirect_uri' => route('facebook.callback')]);

        $response = $this->actingAs($user)->get(route('settings'));
        $this->assertStringNotContainsString($unique, $response->getContent());
    }

    public function test_update_without_secret_keeps_existing()
    {
        $user = User::factory()->create();
        MetaProviderSetting::create(['user_id' => $user->id, 'app_id' => '123', 'app_secret_encrypted' => Crypt::encryptString('orig'), 'redirect_uri' => route('facebook.callback')]);

        $this->actingAs($user)->put(route('settings.meta.update'), ['app_id' => '456']);

        $row = MetaProviderSetting::where('user_id', $user->id)->first();
        $this->assertEquals('orig', Crypt::decryptString($row->app_secret_encrypted));
        $this->assertEquals('456', $row->app_id);
    }

    public function test_meta_test_without_config_returns_configuration_required()
    {
        $user = User::factory()->create();
        $payload = ['app_id' => '', 'app_secret' => ''];
        $response = $this->actingAs($user)->postJson(route('settings.meta.test'), $payload);
        $response->assertStatus(422);
        $response->assertJson(['status' => 'configuration_required']);
    }

    public function test_meta_test_success_with_mock_http()
    {
        $user = User::factory()->create();
        MetaProviderSetting::create(['user_id' => $user->id, 'app_id' => '123', 'app_secret_encrypted' => Crypt::encryptString('abc'), 'redirect_uri' => route('facebook.callback')]);

        Http::fake(function ($request) {
            return Http::response(['access_token' => 'app|token'], 200);
        });

        $response = $this->actingAs($user)->postJson(route('settings.meta.test'), []);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
