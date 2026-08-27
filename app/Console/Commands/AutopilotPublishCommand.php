<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\MetaFacebookService;
use Illuminate\Console\Command;

class AutopilotPublishCommand extends Command
{
    protected $signature = 'autopilot:publish {--limit=10}';

    protected $description = 'Publish ready posts via the configured Meta/Facebook integration';

    public function handle(MetaFacebookService $facebookService): int
    {
        // Prevent dispatching real publish jobs when Meta credentials are not configured
        if (! $facebookService->hasCredentials()) {
            $this->warn('Meta credentials not configured. Skipping publish dispatch.');
            return self::SUCCESS;
        }

        $posts = Post::query()
            ->where('status', 'ready')
            ->orderBy('scheduled_at', 'asc')
            ->limit((int) $this->option('limit'))
            ->get();

        $dispatched = 0;
        foreach ($posts as $post) {
            \App\Jobs\PublishFacebookPostJob::dispatch($post->id);
            $dispatched++;
        }

        $this->info('Dispatched publish jobs for '.$dispatched.' posts.');

        return self::SUCCESS;
    }
}
