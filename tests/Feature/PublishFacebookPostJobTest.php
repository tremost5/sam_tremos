<?php

namespace Tests\Feature;

use App\Jobs\PublishFacebookPostJob;
use App\Models\Post;
use App\Models\User;
use App\Services\MetaFacebookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishFacebookPostJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_job_uses_mock_meta_service_and_is_idempotent()
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Publish test',
            'caption' => 'Hello world',
            'status' => 'ready',
            'facebook_page_id' => 'page_1',
        ]);

        // Bind a fake MetaFacebookService
        $this->app->instance(MetaFacebookService::class, new class extends MetaFacebookService {
            public function hasCredentials(): bool { return true; }
            public function publish($post, $pageId = null): array
            {
                return ['status' => 'success', 'post_id' => 'fb_test_123', 'page_id' => $pageId];
            }
        });

        $job = new PublishFacebookPostJob($post->id);
        $service = $this->app->make(MetaFacebookService::class);

        $job->handle($service);

        $fresh = $post->fresh();
        $this->assertEquals('published', $fresh->status);
        $this->assertEquals('fb_test_123', $fresh->facebook_post_id);
        $this->assertNotNull($fresh->published_at);

        $publishedAt = $fresh->published_at;

        // Run again - should be idempotent and not change the facebook_post_id or published_at
        $job->handle($service);

        $fresh2 = $post->fresh();
        $this->assertEquals('fb_test_123', $fresh2->facebook_post_id);
        $this->assertEquals($publishedAt->toDateTimeString(), $fresh2->published_at->toDateTimeString());
    }
}
