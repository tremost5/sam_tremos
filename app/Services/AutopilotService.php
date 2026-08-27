<?php

namespace App\Services;

use App\Models\AutopilotSetting;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class AutopilotService
{
    protected AIContentEngine $engine;
    protected ContentQualityService $qualityService;
    protected ContentSimilarityService $similarityService;
    protected ImageGenerationService $imageGenerationService;

    public function __construct(
        ?AIContentEngine $engine = null,
        ?ContentQualityService $qualityService = null,
        ?ContentSimilarityService $similarityService = null,
        ?ImageGenerationService $imageGenerationService = null,
    ) {
        $this->engine = $engine ?? new AIContentEngine();
        $this->qualityService = $qualityService ?? new ContentQualityService();
        $this->similarityService = $similarityService ?? new ContentSimilarityService();
        $this->imageGenerationService = $imageGenerationService ?? new ImageGenerationService();
    }

    public function runDue(): int
    {
        $total = 0;

        foreach (AutopilotSetting::query()->with('user')->where('enabled', true)->get() as $setting) {
            if ($setting->user) {
                $result = $this->runForUser($setting->user);
                $total += (int) ($result['generated'] ?? 0);
            }
        }

        return $total;
    }

    public function runForUser(User $user): array
    {
        $setting = AutopilotSetting::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'enabled' => false,
                'mode' => 'manual',
                'posts_per_day' => 2,
                'timezone' => 'Asia/Jakarta',
                'language' => 'id',
                'tone' => 'santai',
                'image_enabled' => true,
                'auto_publish' => false,
                'require_approval' => true,
                'minimum_quality_score' => 75,
                'minimum_inventory' => 5,
                'target_inventory' => 14,
                'categories' => ['Tips Mancing', 'Nila', 'Mujair', 'Fishing Lifestyle'],
            ]
        );

        if (! $setting->enabled) {
            return ['generated' => 0, 'reason' => 'autopilot_disabled'];
        }

        // Ensure AI provider/configuration is present before queuing any generation
        if (! $this->engine->isProviderConfigured()) {
            return ['generated' => 0, 'reason' => 'configuration_required', 'message' => 'AI provider belum dikonfigurasi. Set AI_API_KEY dan AI_TEXT_MODEL terlebih dahulu.'];
        }

        // Count current inventory: only statuses considered part of inventory are 'ready' and 'scheduled'
        $inventory = Post::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['ready', 'scheduled'])
            ->count();

        $targetInventory = (int) ($setting->target_inventory ?? 14);
        $minimumInventory = (int) ($setting->minimum_inventory ?? 5);

        if ($inventory >= $targetInventory) {
            return ['generated' => 0, 'reason' => 'inventory_full'];
        }

        // Determine how many to generate to reach the target inventory
        $toGenerate = max(0, $targetInventory - $inventory);

        $categories = $setting->categories ?: ['Tips Mancing', 'Nila', 'Mujair'];
        $generated = 0;

        // Queue generation jobs rather than creating content synchronously
        foreach (array_slice($categories, 0, max(1, (int) ($setting->posts_per_day ?? 1))) as $category) {
            if ($generated >= $toGenerate) {
                break;
            }

            $context = [
                'category' => $category,
                'category_id' => null,
                'language' => $setting->language ?? 'id',
                'tone' => $setting->tone ?? 'santai',
                'image_enabled' => (bool) $setting->image_enabled,
            ];

            \App\Jobs\GenerateContentJob::dispatch($user->id, $context);
            $generated++;
        }

        return ['generated' => $generated];
    }
}
