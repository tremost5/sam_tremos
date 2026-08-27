<?php

namespace Tests\Feature;

use App\Models\FacebookPage;
use App\Models\MetaProviderSetting;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_routes_are_public(): void
    {
        $this->get('/privacy-policy')->assertOk()->assertSee('Kebijakan Privasi');
        $this->get('/data-deletion')->assertOk()->assertSee('Penghapusan Data');
        $this->get('/terms')->assertOk()->assertSee('Syarat Layanan');
    }

    public function test_facebook_connect_stores_oauth_state(): void
    {
        $user = User::factory()->create();
        MetaProviderSetting::create([
            'user_id' => $user->id,
            'app_id' => '2518630451917892',
            'app_secret_encrypted' => Crypt::encryptString('test-secret'),
            'redirect_uri' => 'https://malang-web.site/pilotfb/facebook/callback',
        ]);

        $response = $this->actingAs($user)->get('/facebook/connect');
        $response->assertRedirect();
        $response->assertSessionHas('facebook_oauth_state');
        $this->assertStringNotContainsString('test-secret', $response->headers->get('Location'));
    }

    public function test_facebook_callback_rejects_missing_or_invalid_state(): void
    {
        $user = User::factory()->create();
        MetaProviderSetting::create([
            'user_id' => $user->id,
            'app_id' => '2518630451917892',
            'app_secret_encrypted' => Crypt::encryptString('test-secret'),
            'redirect_uri' => 'https://malang-web.site/pilotfb/facebook/callback',
        ]);

        $response = $this->actingAs($user)->get('/facebook/callback?code=test-code&state=invalid');
        $response->assertRedirect('/facebook');
        $response->assertSessionHas('error', 'Facebook OAuth tidak valid atau sudah kedaluwarsa.');
    }

    public function test_publish_requires_a_selected_user_page(): void
    {
        Http::fake();
        $user = User::factory()->create();
        MetaProviderSetting::create([
            'user_id' => $user->id,
            'app_id' => '2518630451917892',
            'app_secret_encrypted' => Crypt::encryptString('test-secret'),
            'redirect_uri' => 'https://malang-web.site/pilotfb/facebook/callback',
        ]);
        $post = Post::create(['user_id' => $user->id, 'title' => 'Test', 'status' => 'ready', 'ai_generated' => true]);

        $result = app(\App\Services\MetaFacebookService::class)->publish($post);

        $this->assertSame('configuration_required', $result['status']);
        $this->assertSame('Facebook Page belum dipilih.', $result['message']);
        Http::assertNothingSent();
    }
}
