<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ImageGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $postId,
        public string $prompt,
        public ?int $userId = null,
    ) {}

    public function handle(ImageGenerationService $service): void
    {
        $post = Post::query()->find($this->postId);
        if (! $post) {
            return;
        }

        // Apply user-specific AI config into runtime before generating image
        $uid = $this->userId ?? $post->user_id;
        if (! empty($uid)) {
            $ai = \App\Models\AiProviderSetting::where('user_id', $uid)->first();
            if ($ai && $ai->hasApiKey()) {
                config(['services.ai.api_key' => $ai->getApiKey()]);
                if (! empty($ai->image_model)) {
                    config(['services.ai.image_model' => $ai->image_model]);
                }
            }
        }

        $result = $service->generate($this->prompt);
        $status = $result['status'] ?? 'failed';

        if ($status === 'success' && ! empty($result['path'])) {
            $post->update(['image_path' => $result['path']]);
            return;
        }

        if ($status === 'configuration_required') {
            // Record that image provider is not configured; do not mark as success
            $post->update(['error_message' => 'image_configuration_required']);
            return;
        }

        // For other failures, record message
        $post->update(['error_message' => 'image_generation_failed: '.($result['message'] ?? 'unknown')]);
    }
}
