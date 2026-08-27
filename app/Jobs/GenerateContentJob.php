<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\AIContentEngine;
use App\Services\ContentQualityService;
use App\Services\ContentSimilarityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Jobs\GenerateImageJob;
use App\Jobs\ValidateContentJob;

class GenerateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public array $context = [],
    ) {}

    public function handle(AIContentEngine $engine, ContentQualityService $qualityService, ContentSimilarityService $similarityService): void
    {
        // Prevent creating AI-generated content when provider is not configured for this user.
        if (! $engine->isProviderConfigured(/* check env defaults first */)) {
            // check user-specific config
            if (! $engine->isProviderConfiguredForUser($this->userId ?? null)) {
                \Log::warning('AI provider not configured for user, skipping content generation job', ['user_id' => $this->userId]);
                return;
            }
        }

        $userPosts = Post::query()->where('user_id', $this->userId)->orderByDesc('created_at')->limit(20)->get();
        $history = $userPosts->pluck('title')->all();

        $package = $engine->generateContentPackage([
            ...$this->context,
            'user_id' => $this->userId,
            'history' => $history,
            'language' => $this->context['language'] ?? 'id',
            'tone' => $this->context['tone'] ?? 'santai',
        ]);

        $similarity = $similarityService->isDuplicate($package['title'].' '.$package['idea'], $history);
        if ($similarity) {
            $package['title'] = $package['title'].' '.Str::random(4);
        }

        $score = $qualityService->score($package);
        $package['quality_score'] = $score;

        $post = Post::query()->create([
            'user_id' => $this->userId,
            'title' => $package['title'],
            'idea' => $package['idea'],
            'category_id' => $this->context['category_id'] ?? null,
            'caption' => $package['caption'],
            'hashtags' => $package['hashtags'],
            'engagement_question' => $package['engagement_question'],
            'image_prompt' => $package['image_prompt'],
            'quality_score' => $score,
            'status' => 'draft',
            'ai_generated' => true,
        ]);

        // Dispatch image generation and validation asynchronously
        if (! empty($this->context['image_enabled'])) {
            GenerateImageJob::dispatch($post->id, $package['image_prompt'] ?? $package['title'], $this->userId);
        }

        // Validate content after creation (score/duplicate checks)
        ValidateContentJob::dispatch($post->id);
    }
}
