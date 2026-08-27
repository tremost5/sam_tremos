<?php

namespace App\Services\Providers;

use App\Contracts\AITextProvider;
use Illuminate\Support\Facades\Http;

class OpenAiCompatibleTextProvider implements AITextProvider
{
    public function name(): string
    {
        return 'openai';
    }

    public function generateText(string $prompt): array
    {
        $apiKey = trim((string) config('services.ai.api_key'));
        $baseUrl = trim((string) config('services.ai.base_url', 'https://api.openai.com/v1'));
        $model = trim((string) config('services.ai.text_model'));

        if ($apiKey === '' || $model === '') {
            return ['status' => 'configuration_required', 'message' => 'AI provider belum dikonfigurasi.'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl.'/chat/completions', [
            'model' => $model,
            'messages' => [[
                'role' => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.8,
            'max_tokens' => 1200,
        ]);

        if ($response->failed()) {
            return ['status' => 'provider_error', 'message' => 'AI text provider gagal merespons.'];
        }

        $payload = $response->json();
        $text = $payload['choices'][0]['message']['content'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            return ['status' => 'invalid_response', 'message' => 'AI text provider mengembalikan respons yang tidak valid.'];
        }

        return ['status' => 'success', 'data' => trim($text)];
    }

    public function generateJson(string $prompt, array $schema = []): array
    {
        $apiKey = trim((string) config('services.ai.api_key'));
        $baseUrl = trim((string) config('services.ai.base_url', 'https://api.openai.com/v1'));
        $model = trim((string) config('services.ai.text_model'));

        if ($apiKey === '' || $model === '') {
            return ['status' => 'configuration_required', 'message' => 'AI provider belum dikonfigurasi.'];
        }

        $request = [
            'model' => $model,
            'messages' => [[
                'role' => 'system',
                'content' => 'Kembalikan respons JSON valid tanpa markdown dan tanpa kata pengantar. Fokus pada niche fishing / mancing Indonesia dan brand Sam Tremos.',
            ], [
                'role' => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object'],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUrl.'/chat/completions', $request);

        if ($response->failed()) {
            return ['status' => 'provider_error', 'message' => 'AI provider gagal merespons.'];
        }

        $payload = $response->json();
        $content = $payload['choices'][0]['message']['content'] ?? null;

        if (! is_string($content) || trim($content) === '') {
            return ['status' => 'invalid_response', 'message' => 'AI text provider mengembalikan respons yang tidak valid.'];
        }

        $decoded = $this->repairJson($content);

        if (! is_array($decoded)) {
            return ['status' => 'invalid_response', 'message' => 'AI response tidak valid JSON.'];
        }

        return ['status' => 'success', 'data' => $decoded];
    }

    protected function repairJson(string $content): mixed
    {
        $trimmed = trim($content);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed);
            $trimmed = preg_replace('/\s*```\s*$/', '', $trimmed);
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        preg_match('/\{.*\}/s', $trimmed, $matches);
        if (! empty($matches[0])) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
