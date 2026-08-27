<?php
// Safe real provider verification script.
// - Does not print secrets
// - Prints a JSON summary of checks and results

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AIContentEngine;
use App\Services\ImageGenerationService;
use App\Services\MetaFacebookService;
use App\Jobs\PublishFacebookPostJob;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

$result = [
    'ai_text' => ['configured' => false, 'status' => 'credential_required', 'package' => null],
    'ai_image' => ['configured' => false, 'status' => 'credential_required', 'path' => null],
    'meta' => ['configured' => false, 'has_pages' => false, 'pages' => []],
];

// Check AI config
$aiApiKey = trim((string) config('services.ai.api_key'));
$aiTextModel = trim((string) config('services.ai.text_model'));
$aiImageModel = trim((string) config('services.ai.image_model'));

if ($aiApiKey !== '' && $aiTextModel !== '') {
    $result['ai_text']['configured'] = true;
    try {
        $engine = new AIContentEngine();
        $package = $engine->generateContentPackage([
            'category' => 'Fishing / Mancing',
            'language' => 'id',
            'tone' => 'santai',
            // history empty for deterministic fresh output
            'history' => [],
        ]);

        // Verify required fields
        $ok = isset($package['title'], $package['idea'], $package['caption'], $package['hashtags'], $package['engagement_question'], $package['image_prompt']);
        $result['ai_text']['status'] = $ok ? 'success' : 'invalid_package';
        if ($ok) {
            // Include package but remove any long content if needed
            $result['ai_text']['package'] = [
                'title' => $package['title'],
                'idea' => $package['idea'],
                'caption' => mb_strimwidth($package['caption'], 0, 200, '...'),
                'hashtags' => $package['hashtags'],
                'engagement_question' => $package['engagement_question'],
                'image_prompt' => $package['image_prompt'],
                'quality_score' => $package['quality_score'] ?? null,
            ];
        } else {
            $result['ai_text']['package'] = $package;
        }
    } catch (\Throwable $e) {
        $result['ai_text']['status'] = 'error';
        $result['ai_text']['error'] = $e->getMessage();
    }
}

if ($aiApiKey !== '' && $aiImageModel !== '') {
    $result['ai_image']['configured'] = true;
    try {
        if (empty($result['ai_text']['package']['image_prompt'])) {
            $prompt = 'Cinematic realistic outdoor fishing photograph, Indonesian lake, angler, natural light.';
        } else {
            $prompt = $result['ai_text']['package']['image_prompt'];
        }

        $imageService = new ImageGenerationService();
        $gen = $imageService->generate($prompt, 'generated');

        $result['ai_image']['status'] = $gen['status'] ?? 'failed';
        $result['ai_image']['path'] = $gen['path'] ?? null;
        if (! empty($gen['path'])) {
            $result['ai_image']['exists'] = Storage::disk('public')->exists($gen['path']);
        }
    } catch (\Throwable $e) {
        $result['ai_image']['status'] = 'error';
        $result['ai_image']['error'] = $e->getMessage();
    }
}

// Meta check
$metaService = new MetaFacebookService();
if ($metaService->hasCredentials()) {
    $result['meta']['configured'] = true;
    try {
        $pages = $metaService->getPages();
        // Remove tokens from pages
        $safePages = [];
        foreach ($pages as $p) {
            $safePages[] = [
                'id' => $p['id'] ?? null,
                'name' => $p['name'] ?? null,
            ];
        }
        $result['meta']['has_pages'] = count($safePages) > 0;
        $result['meta']['pages'] = $safePages;

        // If pages present, attempt a single test publish using a temporary Post
        if ($result['meta']['has_pages']) {
            // create test post
            $testPost = Post::create([
                'user_id' => 1,
                'title' => 'Test publish - do not keep',
                'caption' => 'This is a test publish from local verification script. Please ignore.',
                'status' => 'ready',
                'facebook_page_id' => $safePages[0]['id'] ?? null,
            ]);

            // run publish job synchronously
            $job = new PublishFacebookPostJob($testPost->id);
            try {
                $job->handle($metaService);
                $fresh = $testPost->fresh();
                $result['meta']['publish'] = [
                    'status' => $fresh->status,
                    'facebook_post_id' => $fresh->facebook_post_id ? 'RECEIVED' : null,
                ];

                // Run again to verify idempotency
                $job->handle($metaService);
                $fresh2 = $testPost->fresh();
                $result['meta']['publish_repeat'] = [
                    'status' => $fresh2->status,
                    'facebook_post_id' => $fresh2->facebook_post_id ? 'RECEIVED' : null,
                ];
            } catch (\Throwable $e) {
                $result['meta']['publish'] = ['status' => 'failed', 'error' => $e->getMessage()];
            }
        }
    } catch (\Throwable $e) {
        $result['meta']['error'] = $e->getMessage();
    }
}

// Print summary JSON
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
