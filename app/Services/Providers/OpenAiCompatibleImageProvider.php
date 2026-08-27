<?php

namespace App\Services\Providers;

use App\Contracts\AIImageProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAiCompatibleImageProvider implements AIImageProvider
{
    public function name(): string
    {
        return 'openai_image';
    }

    public function generate(string $prompt, array $options = []): array
    {
        $apiKey = trim((string) config('services.ai.api_key'));
        $baseUrl = trim((string) config('services.ai.base_url', 'https://api.openai.com/v1'));
        $model = trim((string) config('services.ai.image_model'));

        if ($apiKey === '' || $model === '') {
            return ['status' => 'configuration_required', 'message' => 'AI provider belum dikonfigurasi.'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl.'/images/generations', [
            'model' => $model,
            'prompt' => $prompt,
            'size' => $options['size'] ?? '1024x1024',
            'quality' => $options['quality'] ?? 'standard',
            'n' => $options['n'] ?? 1,
        ]);

        if ($response->failed()) {
            return ['status' => 'provider_error', 'message' => 'AI image provider gagal merespons.'];
        }

        $payload = $response->json();
        $imageUrl = $payload['data'][0]['url'] ?? null;

        if (! is_string($imageUrl) || trim($imageUrl) === '') {
            return ['status' => 'invalid_response', 'message' => 'AI image provider tidak mengembalikan URL gambar.'];
        }

        return ['status' => 'success', 'data' => ['url' => $imageUrl, 'source' => 'remote']];
    }
}
