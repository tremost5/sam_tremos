<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SanitizeSensitiveConfig
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Sanitize HTML-like responses (strings, View, or Response)
        $sensitive = [
            (string) config('services.ai.api_key'),
            (string) config('services.meta.app_secret'),
            (string) config('services.meta.access_token'),
        ];

        $sensitive = array_filter($sensitive);

        if (! empty($sensitive)) {
            $content = (string) $response;
            if (str_contains($content, '<') && str_contains($content, '>')) {
                $sanitized = str_replace($sensitive, '', $content);

                // If response was a Response instance, preserve headers and status
                if ($response instanceof Response) {
                    $response->setContent($sanitized);
                } else {
                    $response = response($sanitized);
                }
            }
        }

        return $response;
    }
}
