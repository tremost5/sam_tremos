<?php

namespace Tests\Feature;

use App\Models\AutopilotSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_settings()
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_settings()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan');
    }

    public function test_user_can_save_autopilot_setting()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put('/settings', [
            'enabled' => 1,
            'mode' => 'semi',
            'posts_per_day' => 3,
            'timezone' => 'Asia/Jakarta',
            'language' => 'id',
            'tone' => 'santai',
            'categories' => ['Tips Mancing','Nila'],
        ]);

        $this->assertDatabaseHas('autopilot_settings', ['user_id' => $user->id, 'mode' => 'semi']);
    }

    public function test_user_cannot_modify_other_users_settings()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Create setting for user B
        AutopilotSetting::create(['user_id' => $userB->id, 'enabled' => false]);

        // User A attempts to update settings - should only affect his own
        $this->actingAs($userA)->put('/settings', ['enabled' => 1]);

        $this->assertDatabaseHas('autopilot_settings', ['user_id' => $userA->id, 'enabled' => 1]);
        $this->assertDatabaseHas('autopilot_settings', ['user_id' => $userB->id, 'enabled' => false]);
    }

    public function test_api_key_and_meta_secret_not_shown_in_html()
    {
        $user = User::factory()->create();
        config(['services.ai.api_key' => 'super-secret', 'services.meta.app_secret' => 'meta-secret']);

        $response = $this->actingAs($user)->get('/settings');
        $content = $response->getContent();

        $this->assertStringNotContainsString('super-secret', $content);
        $this->assertStringNotContainsString('meta-secret', $content);
    }

    public function test_dashboard_shows_configuration_status()
    {
        $user = User::factory()->create();
        config(['services.ai.api_key' => '', 'services.ai.text_model' => '']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('AI:');
    }
}
