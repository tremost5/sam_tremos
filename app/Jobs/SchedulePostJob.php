<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SchedulePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $postId,
        public ?string $scheduledAt = null,
    ) {}

    public function handle(): void
    {
        $post = Post::query()->find($this->postId);
        if (! $post) {
            return;
        }

        $post->update([
            'status' => 'scheduled',
            'scheduled_at' => $this->scheduledAt ? now()->parse($this->scheduledAt) : now()->addHours(6),
        ]);
    }
}
