<?php

namespace Tests\Feature;

use App\Models\AutopilotSetting;
use App\Models\User;
use App\Services\AutopilotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutopilotServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_autopilot_can_generate_inventory_for_a_user(): void
    {
        // Ensure AI provider appears configured for this test so Autopilot will queue jobs
        config(['services.ai.api_key' => 'test-key', 'services.ai.text_model' => 'gpt-test']);

        $user = User::factory()->create();

        AutopilotSetting::create([
            'user_id' => $user->id,
            'enabled' => true,
            'posts_per_day' => 2,
            'mode' => 'auto',
            'minimum_quality_score' => 70,
            'minimum_inventory' => 1,
            'target_inventory' => 2,
            'language' => 'id',
            'tone' => 'santai',
            'image_enabled' => true,
            'auto_publish' => false,
            'require_approval' => false,
            'categories' => ['Tips Mancing', 'Nila'],
        ]);

        $service = new AutopilotService();
        $result = $service->runForUser($user);

        $this->assertGreaterThanOrEqual(1, $result['generated'] ?? 0);
        $this->assertNotNull($user->fresh());
    }
}
