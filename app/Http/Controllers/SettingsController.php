<?php

namespace App\Http\Controllers;

use App\Models\AutopilotSetting;
use App\Services\AIContentEngine;
use App\Services\MetaFacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\AiProviderSetting;
use App\Models\MetaProviderSetting;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function __construct()
    {
    }

    public function index(AIContentEngine $engine, MetaFacebookService $facebookService)
    {
        $user = Auth::user();

        $autopilot = AutopilotSetting::firstOrCreate(
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

            $aiStatus = $engine->isProviderConfigured() ? 'Terpasang' : 'Belum dikonfigurasi';
            $aiTextModel = config('services.ai.text_model');
            $aiImageModel = config('services.ai.image_model');

            $facebookStatus = $facebookService->hasCredentials($user) ? 'Configured' : 'Not Configured';

            // Null out config to reduce risk for any later code
            config(['services.ai.api_key' => null, 'services.meta.app_secret' => null, 'services.meta.access_token' => null]);

            // Load AI provider settings for current user (do not decrypt key)
            $ai = AiProviderSetting::where('user_id', $user->id)->first();

            $aiProvider = $ai?->provider ?? 'openai';
            $aiTextModel = $ai?->text_model ?? $aiTextModel;
            $aiImageModel = $ai?->image_model ?? $aiImageModel;
            $aiHasKey = $ai?->hasApiKey() ? true : false;

            // Load Meta provider settings for current user (do not decrypt secret)
            $meta = MetaProviderSetting::where('user_id', $user->id)->first();
            $metaAppId = $meta?->app_id;
            $metaHasSecret = $meta?->hasAppSecret() ? true : false;
            $metaRedirect = $meta?->redirect_uri ?? route('facebook.callback');

                return view('settings.index', compact('autopilot', 'aiStatus', 'aiProvider', 'aiTextModel', 'aiImageModel', 'aiHasKey', 'facebookStatus', 'metaAppId', 'metaHasSecret', 'metaRedirect'));
    }

    // Update AI provider configuration (per-user)
    public function updateAi(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'provider' => ['required', 'string', 'max:50'],
            'text_model' => ['required', 'string', 'max:150'],
            'image_model' => ['nullable', 'string', 'max:150'],
            'api_key' => ['nullable', 'string', 'max:500'],
        ]);

        $record = AiProviderSetting::firstOrNew(['user_id' => $user->id]);
        $record->provider = $data['provider'];
        $record->text_model = $data['text_model'];
        $record->image_model = $data['image_model'] ?? null;

        // Only overwrite api key if provided
        if (! empty($data['api_key'])) {
            $record->api_key_encrypted = Crypt::encryptString($data['api_key']);
        }

        $record->save();

        return redirect()->route('settings')->with(['success' => 'AI configuration berhasil disimpan.', 'ai_success' => true]);
    }

    // Test AI connection using current user's saved credentials or provided payload
    public function testAi(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'provider' => ['required', 'string', 'max:50'],
            'text_model' => ['required', 'string', 'max:150'],
            'api_key' => ['nullable', 'string', 'max:500'],
        ]);

        // Resolve effective api key: prefer provided, then saved, then env
        $ai = AiProviderSetting::where('user_id', $user->id)->first();
        $key = $data['api_key'] ?? null;
        if (empty($key) && $ai?->hasApiKey()) {
            $key = $ai->getApiKey();
        }

        if (empty($key) || empty($data['text_model'])) {
            return response()->json(['status' => 'configuration_required', 'message' => 'AI credential belum lengkap.'], 422);
        }

        // Perform a lightweight test request to the provider using Http (no model-specific client)
        $baseUrl = trim((string) config('services.ai.base_url', 'https://api.openai.com/v1'));

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$key,
                'Content-Type' => 'application/json',
            ])->post($baseUrl.'/chat/completions', [
                'model' => $data['text_model'],
                'messages' => [[
                    'role' => 'user',
                    'content' => 'Hello',
                ]],
                'max_tokens' => 1,
            ]);

            if ($response->successful()) {
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'success', 'message' => 'AI connection berhasil.']);
                }

                return redirect()->route('settings')->with('ai_test_success', 'AI connection berhasil.');
            }

            if ($request->expectsJson()) {
                return response()->json(['status' => 'failed', 'message' => 'AI connection gagal.'], 400);
            }

            return redirect()->route('settings')->with('ai_test_error', 'AI connection gagal.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'failed', 'message' => 'AI connection gagal.'], 400);
            }

            return redirect()->route('settings')->with('ai_test_error', 'AI connection gagal.');
        }
    }

    // Update Meta configuration
    public function updateMeta(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'app_id' => ['required', 'string', 'max:255'],
            'app_secret' => ['nullable', 'string', 'max:1000'],
            'redirect_uri' => ['nullable', 'url', 'max:2000'],
        ]);

        $record = MetaProviderSetting::firstOrNew(['user_id' => $user->id]);
        $record->app_id = $data['app_id'];
        $record->redirect_uri = $data['redirect_uri'] ?? $record->redirect_uri ?? route('facebook.callback');

        if (! empty($data['app_secret'])) {
            $record->setAppSecret($data['app_secret']);
        }

        $record->save();

        return redirect()->route('settings')->with(['success' => 'Konfigurasi Meta berhasil disimpan.', 'meta_success' => true]);
    }

    public function testMeta(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'app_id' => ['nullable', 'string', 'max:255'],
            'app_secret' => ['nullable', 'string', 'max:1000'],
            'redirect_uri' => ['nullable', 'url', 'max:2000'],
        ]);

        // Resolve effective creds: prefer payload, then saved
        $meta = MetaProviderSetting::where('user_id', $user->id)->first();
        $appId = $data['app_id'] ?? $meta?->app_id;
        $appSecret = $data['app_secret'] ?? ($meta?->getAppSecret() ?? null);
        $redirect = $data['redirect_uri'] ?? $meta?->redirect_uri ?? route('facebook.callback');

        if (empty($appId) || empty($appSecret) || empty($redirect)) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'configuration_required', 'message' => empty($appId) ? 'Meta App ID belum dikonfigurasi.' : (empty($appSecret) ? 'Meta App Secret belum dikonfigurasi.' : 'Redirect URI belum dikonfigurasi.')], 422);
            }
            return redirect()->route('settings')->with('ai_test_error', 'Meta App belum dikonfigurasi.');
        }

        // Perform lightweight validation: request app access token
        $graphVersion = config('services.meta.graph_version', 'v19.0');
        $url = "https://graph.facebook.com/{$graphVersion}/oauth/access_token";

        try {
            $resp = Http::asForm()->post($url, [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'grant_type' => 'client_credentials',
            ]);

            if ($resp->successful() && isset($resp->json()['access_token'])) {
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'success', 'message' => 'Meta connection berhasil.']);
                }
                return redirect()->route('settings')->with('meta_test_success', 'Meta connection berhasil.');
            }

            if ($request->expectsJson()) {
                return response()->json(['status' => 'failed', 'message' => 'Meta connection gagal.'], 400);
            }

            return redirect()->route('settings')->with('meta_test_error', 'Meta connection gagal.');
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'failed', 'message' => 'Meta connection gagal.'], 400);
            }
            return redirect()->route('settings')->with('meta_test_error', 'Meta connection gagal.');
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'mode' => ['nullable', 'string'],
            'posts_per_day' => ['nullable', 'integer'],
            'timezone' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'tone' => ['nullable', 'string'],
            'image_enabled' => ['nullable', 'boolean'],
            'auto_publish' => ['nullable', 'boolean'],
            'require_approval' => ['nullable', 'boolean'],
            'minimum_quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'minimum_inventory' => ['nullable', 'integer', 'min:0'],
            'target_inventory' => ['nullable', 'integer', 'min:1'],
            'categories' => ['nullable'], // accept comma-separated string or array
        ]);

        // Support comma-separated categories from the form
        if (isset($data['categories']) && is_string($data['categories'])) {
            $data['categories'] = array_filter(array_map('trim', explode(',', $data['categories'])));
        }

        // If categories provided as array, ensure each item is a string
        if (isset($data['categories']) && is_array($data['categories'])) {
            $data['categories'] = array_values(array_filter(array_map(fn($v) => is_scalar($v) ? (string) $v : null, $data['categories'])));
        }

            $payload = [
                'enabled' => isset($data['enabled']) ? (bool) $data['enabled'] : false,
                'mode' => $data['mode'] ?? 'manual',
                'posts_per_day' => $data['posts_per_day'] ?? 1,
                'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
                'language' => $data['language'] ?? 'id',
                'tone' => $data['tone'] ?? 'santai',
                'image_enabled' => isset($data['image_enabled']) ? (bool) $data['image_enabled'] : true,
                'auto_publish' => isset($data['auto_publish']) ? (bool) $data['auto_publish'] : false,
                'require_approval' => isset($data['require_approval']) ? (bool) $data['require_approval'] : true,
                'minimum_quality_score' => $data['minimum_quality_score'] ?? 75,
                'minimum_inventory' => $data['minimum_inventory'] ?? 5,
                'target_inventory' => $data['target_inventory'] ?? 14,
                'categories' => $data['categories'] ?? ['Tips Mancing', 'Nila', 'Mujair'],
            ];

            AutopilotSetting::updateOrCreate(['user_id' => $user->id], $payload);

        return redirect()->route('settings')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
