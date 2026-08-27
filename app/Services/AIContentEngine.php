<?php

namespace App\Services;

use App\Contracts\AITextProvider;
use App\Services\Providers\OpenAiCompatibleTextProvider;
use Illuminate\Support\Facades\Http;
use App\Models\AiProviderSetting;
use Illuminate\Support\Facades\Config;

class AIContentEngine
{
    protected AITextProvider $provider;

    public function __construct(?AITextProvider $provider = null)
    {
        $this->provider = $provider ?? new OpenAiCompatibleTextProvider();
    }

    public function generateIdea(array $context = []): string
    {
        // Apply user-specific AI config if provided in context
        if (! empty($context['user_id'])) {
            $this->applyUserConfig($context['user_id']);
        }

        $category = $context['category'] ?? 'Fishing Lifestyle';
        $tone = $context['tone'] ?? 'santai';
        $language = $context['language'] ?? 'id';
        $priorIdeas = $context['history'] ?? [];

        $prompt = "Buat 1 judul/ide konten Facebook tentang fishing / mancing untuk brand Sam Tremos. Kategori: {$category}. Bahasa: {$language}. Tone: {$tone}. Hindari ide yang mirip dengan histori: ".json_encode($priorIdeas).". Hanya output 1 baris singkat dan jelas.";

        $response = $this->provider->generateText($prompt);
        if (($response['status'] ?? null) !== 'success') {
            throw new \RuntimeException($response['message'] ?? 'AI provider gagal menghasilkan ide.');
        }

        $text = trim((string) ($response['data'] ?? ''));
        if ($text === '') {
            throw new \RuntimeException('AI provider mengembalikan ide kosong.');
        }

        return $text;
    }

    public function generateCaption(array $context = []): string
    {
        if (! empty($context['user_id'])) {
            $this->applyUserConfig($context['user_id']);
        }
        $idea = $context['idea'] ?? 'Tips mancing hari ini';
        $category = $context['category'] ?? 'Fishing Lifestyle';
        $tone = $context['tone'] ?? 'santai';
        $language = $context['language'] ?? 'id';
        $history = $context['history'] ?? [];

        $prompt = "Buat caption Facebook untuk niche fishing/mancing Indonesia dengan brand Sam Tremos. Judul/ide: {$idea}. Kategori: {$category}. Bahasa: {$language}. Tone: {$tone}. Hindari repetisi dengan: ".json_encode($history).". Output hanya caption, max 3 paragraf, natural, santai, autentik, tanpa clickbait berlebihan.";

        $response = $this->provider->generateText($prompt);
        if (($response['status'] ?? null) !== 'success') {
            throw new \RuntimeException($response['message'] ?? 'AI provider gagal menghasilkan caption.');
        }

        $text = trim((string) ($response['data'] ?? ''));
        if ($text === '') {
            throw new \RuntimeException('AI provider mengembalikan caption kosong.');
        }

        return $text;
    }

    public function generateHashtags(array $context = []): string
    {
        if (! empty($context['user_id'])) {
            $this->applyUserConfig($context['user_id']);
        }
        $category = $context['category'] ?? 'Fishing Lifestyle';
        $history = $context['history'] ?? [];

        $prompt = "Buat 6 hashtag untuk konten Facebook niche fishing/mancing Indonesia brand Sam Tremos. Kategori: {$category}. Hindari hashtag yang mirip dengan histori: ".json_encode($history).". Output format satu baris, hashtag dipisah spasi, tanpa tanda baca ekstra.";

        $response = $this->provider->generateText($prompt);
        if (($response['status'] ?? null) !== 'success') {
            throw new \RuntimeException($response['message'] ?? 'AI provider gagal menghasilkan hashtag.');
        }

        $text = trim((string) ($response['data'] ?? ''));
        if ($text === '') {
            throw new \RuntimeException('AI provider mengembalikan hashtag kosong.');
        }

        return $text;
    }

    public function generateEngagementQuestion(array $context = []): string
    {
        if (! empty($context['user_id'])) {
            $this->applyUserConfig($context['user_id']);
        }
        $category = $context['category'] ?? 'Fishing Lifestyle';
        $prompt = "Buat 1 pertanyaan engagement untuk konten Facebook niche fishing/mancing Indonesia brand Sam Tremos. Kategori: {$category}. Output hanya satu kalimat pertanyaan singkat dan natural.";

        $response = $this->provider->generateText($prompt);
        if (($response['status'] ?? null) !== 'success') {
            throw new \RuntimeException($response['message'] ?? 'AI provider gagal menghasilkan pertanyaan engagement.');
        }

        $text = trim((string) ($response['data'] ?? ''));
        if ($text === '') {
            throw new \RuntimeException('AI provider mengembalikan pertanyaan kosong.');
        }

        return $text;
    }

    public function generateImagePrompt(array $context = []): string
    {
        if (! empty($context['user_id'])) {
            $this->applyUserConfig($context['user_id']);
        }
        $category = $context['category'] ?? 'Fishing Lifestyle';
        $idea = $context['idea'] ?? 'tips mancing';
        $prompt = "Buat prompt gambar realistis untuk konten fishing Indonesia brand Sam Tremos. Tema: {$idea}. Kategori: {$category}. Gaya cinematic, natural lights, photorealistic, outdoor fishing, river/lake, angler, high quality, detail, tidak cartoon, Indonesia, masuk akal untuk Facebook.";

        $response = $this->provider->generateText($prompt);
        if (($response['status'] ?? null) !== 'success') {
            throw new \RuntimeException($response['message'] ?? 'AI provider gagal menghasilkan image prompt.');
        }

        $text = trim((string) ($response['data'] ?? ''));
        if ($text === '') {
            throw new \RuntimeException('AI provider mengembalikan image prompt kosong.');
        }

        return $text;
    }

    public function generateContentPackage(array $context = []): array
    {
        $category = $context['category'] ?? 'Fishing Lifestyle';
        $language = $context['language'] ?? 'id';
        $tone = $context['tone'] ?? 'santai';
        $history = $context['history'] ?? [];
        $userId = $context['user_id'] ?? null;
        if ($userId) {
            $this->applyUserConfig((int) $userId);
        }
        $sharedContext = ['category' => $category, 'tone' => $tone, 'language' => $language, 'history' => $history, 'user_id' => $userId];
        $idea = $context['idea'] ?? $this->generateIdea($sharedContext);
        $title = $context['title'] ?? trim((string) str($idea)->limit(60));

        $caption = $this->generateCaption([...$sharedContext, 'idea' => $idea]);
        $hashtags = $this->generateHashtags($sharedContext);
        $engagementQuestion = $this->generateEngagementQuestion([...$sharedContext]);
        $imagePrompt = $this->generateImagePrompt([...$sharedContext, 'idea' => $idea]);
        $qualityScore = $this->calculateQualityScore([
            'originality' => 88,
            'relevance' => 90,
            'readability' => 84,
            'engagement_potential' => 86,
            'visual_relevance' => 92,
        ]);

        return [
            'title' => $title,
            'idea' => $idea,
            'category' => $category,
            'caption' => $caption,
            'hashtags' => $hashtags,
            'engagement_question' => $engagementQuestion,
            'image_prompt' => $imagePrompt,
            'quality_score' => $qualityScore,
            'suggested_posting_time' => now()->addHours(6)->format('Y-m-d H:i:s'),
        ];
    }

    public function validateContent(array $content): bool
    {
        return ! empty($content['title'])
            && ! empty($content['caption'])
            && ! empty($content['hashtags']);
    }

    public function calculateQualityScore(array $metrics): int
    {
        $weighted = (
            ($metrics['originality'] ?? 0) * 0.25 +
            ($metrics['relevance'] ?? 0) * 0.25 +
            ($metrics['readability'] ?? 0) * 0.2 +
            ($metrics['engagement_potential'] ?? 0) * 0.2 +
            ($metrics['visual_relevance'] ?? 0) * 0.1
        );

        return (int) round($weighted);
    }

    public function calculateSimilarity(string $left, string $right): float
    {
        similar_text(strtolower($left), strtolower($right), $percent);

        return (float) $percent;
    }

    public function generateJson(string $prompt, array $schema = []): array
    {
        // If user context provided in schema, apply config
        if (! empty($schema['user_id'])) {
            $this->applyUserConfig($schema['user_id']);
        }
        $response = $this->provider->generateJson($prompt, $schema);

        if (($response['status'] ?? null) !== 'success') {
            return ['status' => $response['status'] ?? 'configuration_required', 'message' => $response['message'] ?? 'AI provider belum dikonfigurasi.'];
        }

        return $response;
    }

    /**
     * Return whether the AI text provider appears configured.
     */
    public function isProviderConfigured(): bool
    {
        $apiKey = trim((string) config('services.ai.api_key'));
        $model = trim((string) config('services.ai.text_model'));

        return $apiKey !== '' && $model !== '';
    }

    public function isProviderConfiguredForUser(?int $userId): bool
    {
        if (empty($userId)) {
            return $this->isProviderConfigured();
        }

        $ai = AiProviderSetting::where('user_id', $userId)->first();
        if ($ai) {
            return ! empty($ai->getApiKey()) && ! empty($ai->text_model);
        }

        return $this->isProviderConfigured();
    }

    /**
     * Apply user-specific AI configuration into runtime config if available.
     */
    public function applyUserConfig(int $userId): void
    {
        $ai = AiProviderSetting::where('user_id', $userId)->first();
        if (! $ai) {
            return;
        }

        $apiKey = $ai->getApiKey();
        if (! empty($apiKey)) {
            Config::set('services.ai.api_key', $apiKey);
        }

        if (! empty($ai->text_model)) {
            Config::set('services.ai.text_model', $ai->text_model);
        }

        if (! empty($ai->image_model)) {
            Config::set('services.ai.image_model', $ai->image_model);
        }
    }
}
