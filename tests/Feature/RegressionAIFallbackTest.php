<?php

namespace Tests\Feature;

use App\Contracts\AITextProvider;
use App\Jobs\GenerateContentJob;
use App\Jobs\GenerateImageJob;
use App\Models\AutopilotSetting;
use App\Models\Post;
use App\Models\User;
use App\Services\AIContentEngine;
use App\Services\AutopilotService;
use App\Services\ContentQualityService;
use App\Services\ContentSimilarityService;
use App\Services\ImageGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RegressionAIFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_configured_engine_and_package_generation()
    {
        // Bind fake AITextProvider that always returns success
        $this->app->instance(AITextProvider::class, new class implements AITextProvider {
            public function name(): string { return 'fake'; }
            public function generateText(string $prompt): array {
                return ['status' => 'success', 'data' => 'Fake response for prompt'];
            }
            public function generateJson(string $prompt, array $schema = []): array {
                return ['status' => 'success', 'data' => ['title' => 'fake', 'idea' => 'fake']];
            }
        });

        // Ensure config appears present so engine treats provider as configured
        config(['services.ai.api_key' => 'ok', 'services.ai.text_model' => 'gpt-test']);

        $engine = $this->app->make(AIContentEngine::class);

        $package = $engine->generateContentPackage(['category' => 'Nila', 'language' => 'id', 'tone' => 'santai']);

        $this->assertNotEmpty($package['title']);
        $this->assertNotEmpty($package['idea']);
        $this->assertNotEmpty($package['caption']);
        $this->assertNotEmpty($package['hashtags']);
        $this->assertNotEmpty($package['engagement_question']);
        $this->assertNotEmpty($package['image_prompt']);
    }

    public function test_ai_not_configured_prevents_job_and_controller_and_autopilot()
    {
        // Ensure AI config is missing
        config(['services.ai.api_key' => '', 'services.ai.text_model' => '']);

        $this->assertFalse($this->app->make(AIContentEngine::class)->isProviderConfigured());

        // Create a user
        $user = User::factory()->create();

        // GenerateContentJob should not create a post when provider missing
        $job = new GenerateContentJob($user->id, ['category' => 'Nila', 'image_enabled' => false]);
        $job->handle($this->app->make(AIContentEngine::class), $this->app->make(ContentQualityService::class), $this->app->make(ContentSimilarityService::class));

        $this->assertSame(0, Post::count(), 'No posts should be created when AI provider missing');

        // Autopilot should return configuration_required and not dispatch jobs
        AutopilotSetting::create([
            'user_id' => $user->id,
            'enabled' => true,
            'posts_per_day' => 1,
            'mode' => 'auto',
            'minimum_inventory' => 1,
            'target_inventory' => 2,
            'language' => 'id',
            'tone' => 'santai',
            'image_enabled' => false,
        ]);

        Queue::fake();
        $service = $this->app->make(AutopilotService::class);
        $result = $service->runForUser($user);

        $this->assertSame(0, $result['generated'] ?? 0);
        $this->assertSame('configuration_required', $result['reason'] ?? '');
        Queue::assertNotPushed(GenerateContentJob::class);

        // AiGenerationController should return configuration error when posting via route
        $response = $this->actingAs($user)->post(route('ai.generate.store'), ['quantity' => 1]);
        $response->assertSessionHas('error');
    }

    public function test_image_configuration_missing_does_not_set_image_path()
    {
        // Ensure image model/config missing
        config(['services.ai.api_key' => '', 'services.ai.text_model' => '', 'services.ai.image_model' => '']);

        $user = User::factory()->create();
        $post = Post::create(['user_id' => $user->id, 'title' => 'T', 'status' => 'draft', 'ai_generated' => true]);

        $job = new GenerateImageJob($post->id, 'a fish on a lake');
        $job->handle($this->app->make(ImageGenerationService::class));

        $fresh = $post->fresh();
        $this->assertNull($fresh->image_path);
        $this->assertSame('image_configuration_required', $fresh->error_message);
    }
}
