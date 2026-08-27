<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ContentQualityService;
use App\Services\ContentSimilarityService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ValidateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $postId,
    ) {}

    public function handle(ContentQualityService $qualityService, ContentSimilarityService $similarityService): void
    {
        $post = Post::query()->find($this->postId);
        if (! $post) {
            return;
        }

        $score = $qualityService->score([
            'title' => $post->title,
            'idea' => $post->idea,
            'caption' => $post->caption,
            'hashtags' => $post->hashtags,
        ]);

        $post->quality_score = $score;
        $post->status = $score >= 75 ? 'ready' : 'draft';
        $post->save();

        $duplicate = Post::query()->where('user_id', $post->user_id)
            ->whereKeyNot($post->id)
            ->get();

        $isDuplicate = $similarityService->isDuplicate($post->title.' '.$post->idea, $duplicate->pluck('title')->all());
        if ($isDuplicate) {
            $post->status = 'draft';
            $post->error_message = 'Konten terdeteksi duplikat. Silakan regenerasi.';
            $post->save();
        }
    }
}
