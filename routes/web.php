<?php

use App\Http\Controllers\AiGenerationController;
use App\Http\Controllers\AutopilotController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\SanitizeSensitiveConfig::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/regenerate', [PostController::class, 'regenerate'])->name('posts.regenerate');
    Route::post('/posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/schedule', [PostController::class, 'schedule'])->name('posts.schedule');
    Route::post('/posts/{post}/cancel', [PostController::class, 'cancel'])->name('posts.cancel');

    Route::get('/ai/generate', [AiGenerationController::class, 'create'])->name('ai.generate');
    Route::post('/ai/generate', [AiGenerationController::class, 'store'])->name('ai.generate.store');

    Route::get('/autopilot', [AutopilotController::class, 'index'])->name('autopilot.index');
    Route::post('/autopilot', [AutopilotController::class, 'update'])->name('autopilot.update');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/facebook', [FacebookController::class, 'index'])->name('facebook.index');
    Route::get('/facebook/connect', [FacebookController::class, 'connect'])->name('facebook.connect');
    Route::get('/facebook/callback', [FacebookController::class, 'callback'])->name('facebook.callback');
    Route::post('/facebook/disconnect', [FacebookController::class, 'disconnect'])->name('facebook.disconnect');
    Route::post('/facebook/pages/{page}/select', [FacebookController::class, 'selectPage'])->name('facebook.pages.select');

    // Settings
    Route::get('/settings', function (\Illuminate\Http\Request $request) {
        $originalAiKey = (string) config('services.ai.api_key');
        $originalMetaSecret = (string) config('services.meta.app_secret');
        $originalMetaAccess = (string) config('services.meta.access_token');

        // Also include per-user stored secrets if present
        $user = $request->user();
        $userAiKey = null;
        $userMetaSecret = null;
        try {
            if ($user) {
                $ai = \App\Models\AiProviderSetting::where('user_id', $user->id)->first();
                if ($ai && $ai->hasApiKey()) {
                    $userAiKey = $ai->getApiKey();
                }
                $meta = \App\Models\MetaProviderSetting::where('user_id', $user->id)->first();
                if ($meta && $meta->hasAppSecret()) {
                    $userMetaSecret = $meta->getAppSecret();
                }
            }
        } catch (\Throwable $e) {
            // noop
        }

        // Call controller action via container to keep DI working
        $controller = app(App\Http\Controllers\SettingsController::class);
        $response = app()->call([$controller, 'index']);

        // Normalize response into HTML string
        $content = $response instanceof \Illuminate\Http\Response ? $response->getContent() : (string) $response;

        // Sanitize any original sensitive values from the rendered HTML
        $sanitized = str_replace(array_filter([$originalAiKey, $originalMetaSecret, $originalMetaAccess, $userAiKey, $userMetaSecret]), '', $content);

        return response($sanitized);
    })->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    // AI settings endpoints
    Route::post('/settings/ai/test', [SettingsController::class, 'testAi'])->name('settings.ai.test');
    Route::put('/settings/ai', [SettingsController::class, 'updateAi'])->name('settings.ai.update');
    // Meta settings endpoints
    Route::post('/settings/meta/test', [SettingsController::class, 'testMeta'])->name('settings.meta.test');
    Route::put('/settings/meta', [SettingsController::class, 'updateMeta'])->name('settings.meta.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
