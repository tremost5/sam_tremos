<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\Providers\OpenAiCompatibleImageProvider;

class ImageGenerationService
{
    /**
     * Generate an image from a prompt.
     * Returns structured array: ['status'=>'success'|'configuration_required'|'failed', 'path'=>string|null, 'message'=>string|null]
     */
    public function generate(string $prompt, ?string $folder = 'generated'): array
    {
      $model = trim((string) config('services.ai.image_model'));

      // Resolve provider from container if bound, otherwise use default OpenAI-compatible provider
      if (app()->bound(\App\Contracts\AIImageProvider::class)) {
          $provider = app()->make(\App\Contracts\AIImageProvider::class);
      } else {
          $provider = new OpenAiCompatibleImageProvider();
      }

      $result = $provider->generate($prompt, ['model' => $model]);

      $status = $result['status'] ?? 'failed';
      if ($status === 'configuration_required') {
          return ['status' => 'configuration_required', 'path' => null, 'message' => $result['message'] ?? 'AI image provider not configured.'];
      }

      if ($status !== 'success') {
          return ['status' => 'failed', 'path' => null, 'message' => $result['message'] ?? 'Image provider error.'];
      }

      $data = $result['data'] ?? [];
      $url = $data['url'] ?? null;
      $base64 = $data['base64'] ?? null;

      try {
          if ($url) {
              $resp = Http::withHeaders([])->get($url);
              if ($resp->failed()) {
                  return ['status' => 'failed', 'path' => null, 'message' => 'Failed to download image.'];
              }

              $contents = $resp->body();
              $contentType = $resp->header('Content-Type', 'image/jpeg');
          } elseif ($base64) {
              $contents = base64_decode($base64);
              $contentType = finfo_buffer(finfo_open(), $contents) ?: 'image/jpeg';
          } else {
              return ['status' => 'failed', 'path' => null, 'message' => 'No image data returned by provider.'];
          }

          $ext = 'jpg';
          if (str_contains((string) $contentType, 'png')) {
              $ext = 'png';
          } elseif (str_contains((string) $contentType, 'svg')) {
              $ext = 'svg';
          }

          $filename = (string) Str::uuid().".{$ext}";
          $relativePath = trim($folder, '/').'/'.$filename;

          Storage::disk('public')->put($relativePath, $contents);

          return ['status' => 'success', 'path' => $relativePath, 'message' => null];
      } catch (\Throwable $e) {
          return ['status' => 'failed', 'path' => null, 'message' => $e->getMessage()];
      }
    }

    protected function buildPlaceholderSvg(string $prompt): string
    {
        $safePrompt = htmlspecialchars(substr($prompt, 0, 180), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675">
  <defs>
    <linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="#0f172a"/>
      <stop offset="50%" stop-color="#1d4ed8"/>
      <stop offset="100%" stop-color="#052e16"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="675" fill="url(#bg)"/>
  <circle cx="190" cy="175" r="120" fill="rgba(255,255,255,0.12)"/>
  <circle cx="1020" cy="150" r="150" fill="rgba(34,197,94,0.18)"/>
  <path d="M0 470 C 150 420, 260 520, 440 470 S 750 400, 1200 500 L1200 675 L0 675 Z" fill="rgba(15,23,42,0.8)"/>
  <rect x="70" y="70" width="1060" height="130" rx="20" fill="rgba(15,23,42,0.42)"/>
  <text x="100" y="140" fill="#f8fafc" font-size="36" font-family="Arial, sans-serif" font-weight="700">Sam Tremos AI Content</text>
  <text x="100" y="560" fill="#e2e8f0" font-size="26" font-family="Arial, sans-serif">{$safePrompt}</text>
</svg>
SVG;
    }
}
