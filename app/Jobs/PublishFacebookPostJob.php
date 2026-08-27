<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\MetaFacebookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishFacebookPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public int $postId,
    ) {}

    public function handle(MetaFacebookService $facebookService): void
    {
        $post = Post::query()->find($this->postId);
        if (! $post) {
            return;
        }

        if (in_array($post->status, ['published'], true) && ! empty($post->facebook_post_id)) {
            return;
        }

        $result = $facebookService->publish($post, $post->facebook_page_id);

        if (($result['status'] ?? null) === 'success') {
            $post->update([
                'status' => 'published',
                'facebook_post_id' => $result['post_id'] ?? null,
                'published_at' => now(),
                'error_message' => null,
            ]);

            return;
        }

        $post->update([
            'status' => 'failed',
            'error_message' => 'Publish Facebook gagal. '.($result['message'] ?? 'Terjadi kesalahan.'),
        ]);

        throw new \RuntimeException('Publish Facebook gagal.');
    }
}
