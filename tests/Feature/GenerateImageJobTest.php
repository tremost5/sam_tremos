<?php

namespace Tests\Feature;

use App\Jobs\GenerateImageJob;
use App\Models\Post;
use App\Models\User;
use App\Contracts\AIImageProvider;
use App\Services\ImageGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GenerateImageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_image_job_calls_provider_and_saves_file()
    {
        Storage::fake('public');
        Http::fake([
            '*' => Http::response('PNGDATA', 200, ['Content-Type' => 'image/png']),
        ]);

        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Test post',
            'status' => 'draft',
            'ai_generated' => true,
        ]);

        // Bind a fake AIImageProvider that returns a remote URL
        $this->app->instance(AIImageProvider::class, new class implements AIImageProvider {
            public function name(): string { return 'fake'; }
            public function generate(string $prompt, array $options = []): array
            {
                return ['status' => 'success', 'data' => ['url' => 'https://example.test/image.png']];
            }
        });

        $job = new GenerateImageJob($post->id, 'a fish on a boat');
        $service = $this->app->make(ImageGenerationService::class);

        $job->handle($service);

        $fresh = $post->fresh();
        $this->assertNotNull($fresh->image_path, 'image_path should be set on post');
        Storage::disk('public')->assertExists($fresh->image_path);
    }
}
