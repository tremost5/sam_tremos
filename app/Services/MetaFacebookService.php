<?php

namespace App\Services;

use App\Models\Post;
use App\Models\MetaProviderSetting;
use Illuminate\Support\Str;

class MetaFacebookService
{
    public function getConfigForUser(?\Illuminate\Contracts\Auth\Authenticatable $user = null): array
    {
        if ($user) {
            $setting = MetaProviderSetting::where('user_id', $user->id)->first();
            return [
                'app_id' => $setting?->app_id,
                'app_secret' => $setting?->getAppSecret(),
                'redirect_uri' => $setting?->redirect_uri ?: config('services.meta.redirect_uri'),
                'graph_version' => config('services.meta.graph_version', 'v19.0'),
            ];
        }

        return [
            'app_id' => config('services.meta.app_id'),
            'app_secret' => config('services.meta.app_secret'),
            'redirect_uri' => config('services.meta.redirect_uri'),
            'graph_version' => config('services.meta.graph_version', 'v19.0'),
        ];
    }

    public function hasCredentials(): bool
    {
        return ! empty(config('services.meta.app_id'))
            && ! empty(config('services.meta.app_secret'))
            && (! empty(config('services.meta.access_token')) || \App\Models\FacebookAccount::query()->exists());
    }

    public function hasCredentialsForUser(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        $config = $this->getConfigForUser($user);

        return ! empty($config['app_id']) && ! empty($config['app_secret']) && ! empty($config['redirect_uri']);
    }

    public function getPages(?\Illuminate\Contracts\Auth\Authenticatable $user = null): array
    {
        if ($user && ! $this->hasCredentialsForUser($user)) {
            return [];
        }
        if (! $user && ! $this->hasCredentials()) {
            return [];
        }

        // For now, prefer DB-stored pages (FacebookPage model) when available
        $pages = [];
        $dbPages = \App\Models\FacebookPage::query()
            ->when($user, fn ($query) => $query->where('user_id', $user->id))
            ->get();
        foreach ($dbPages as $p) {
            $pages[] = ['id' => $p->facebook_id, 'name' => $p->name];
        }

        if (! empty($pages)) return $pages;

        return [];
    }

    public function publish(Post $post, ?string $pageId = null): array
    {
        $user = $post->user_id ? \App\Models\User::find($post->user_id) : null;
        if (! $user || ! $this->hasCredentialsForUser($user)) {
            return [
                'status' => 'configuration_required',
                'message' => 'Meta app credentials are not configured.',
                'page_id' => $pageId ?? config('services.meta.default_page_id'),
            ];
        }

        $page = \App\Models\FacebookPage::query()
            ->where('user_id', $user->id)
            ->where('selected', true)
            ->when($pageId, fn ($query) => $query->where('facebook_id', $pageId))
            ->first();
        $pageId = $page?->facebook_id ?? $pageId;
        if (! $page || ! $pageId) {
            return ['status' => 'configuration_required', 'message' => 'Facebook Page belum dipilih.'];
        }

        // Use only the authenticated user's selected page/account token.
        $accessToken = null;
        if ($page && ! empty($page->access_token)) {
            try {
                $accessToken = \Illuminate\Support\Facades\Crypt::decryptString($page->access_token);
            } catch (\Throwable $e) {
                $accessToken = null;
            }
        }

        if (empty($accessToken)) {
            // Try account-level token
            $account = \App\Models\FacebookAccount::query()->where('user_id', $user->id)->first();
            if ($account && ! empty($account->access_token)) {
                try {
                    $accessToken = \Illuminate\Support\Facades\Crypt::decryptString($account->access_token);
                } catch (\Throwable $e) {
                    $accessToken = null;
                }
            }
        }

        // If still empty, signal configuration required
        if (empty($accessToken)) {
            return [
                'status' => 'configuration_required',
                'message' => 'Meta app credentials are not configured.',
                'page_id' => $pageId,
            ];
        }
        $graphVersion = config('services.meta.graph_version', 'v19.0');

        try {
            if ($post->image_path) {
                // Upload photo
                $url = "https://graph.facebook.com/{$graphVersion}/{$pageId}/photos";
                $response = \Illuminate\Support\Facades\Http::asMultipart()->post($url, [
                    'access_token' => $accessToken,
                    'caption' => $post->caption ?? $post->title,
                    'url' => url(\Illuminate\Support\Facades\Storage::disk('public')->url($post->image_path)),
                ]);
            } else {
                // Text post
                $url = "https://graph.facebook.com/{$graphVersion}/{$pageId}/feed";
                $response = \Illuminate\Support\Facades\Http::asForm()->post($url, [
                    'access_token' => $accessToken,
                    'message' => $post->caption ?? $post->title,
                ]);
            }

            if ($response->failed()) {
                return ['status' => 'provider_error', 'message' => 'Meta Graph API error: '.$response->body()];
            }

            $payload = $response->json();
            $fbId = $payload['post_id'] ?? $payload['id'] ?? null;

            if (! $fbId) {
                return ['status' => 'invalid_response', 'message' => 'Meta Graph did not return post id.'];
            }

            return ['status' => 'success', 'post_id' => $fbId, 'page_id' => $pageId];
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }
}
