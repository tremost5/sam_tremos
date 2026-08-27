<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\AIContentEngine;
use App\Services\MetaFacebookService;
use App\Services\AutopilotService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $engine = app(AIContentEngine::class);
        $facebookService = app(MetaFacebookService::class);
        $autopilotService = app(AutopilotService::class);

        $aiConfigured = $engine->isProviderConfiguredForUser($user->id);
        $facebookConfigured = $facebookService->hasCredentialsForUser($user)
            && \App\Models\FacebookAccount::query()->where('user_id', $user->id)->exists();
        $autopilotEnabled = (bool) \App\Models\AutopilotSetting::query()->where('user_id', $user->id)->where('enabled', true)->exists();
        $stats = [
            'total_posts' => Post::where('user_id', $user->id)->count(),
            'draft' => Post::where('user_id', $user->id)->where('status', 'draft')->count(),
            'scheduled' => Post::where('user_id', $user->id)->where('status', 'scheduled')->count(),
            'published' => Post::where('user_id', $user->id)->where('status', 'published')->count(),
            'failed' => Post::where('user_id', $user->id)->where('status', 'failed')->count(),
            'autopilot_status' => 'Manual',
            'content_inventory' => Post::where('user_id', $user->id)->whereIn('status', ['draft', 'ready', 'scheduled'])->count(),
            'ai_status' => $aiConfigured ? 'Configured' : 'Not configured',
            'facebook_status' => $facebookConfigured ? 'Connected' : 'Not connected',
            'autopilot_active' => $autopilotEnabled,
        ];

        $upcomingPosts = Post::with('category')
            ->where('user_id', $user->id)
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $activity = collect([
            ['label' => 'AI membuat ide', 'time' => '2 jam lalu', 'detail' => 'Tips umpan nila untuk pagi hari'],
            ['label' => 'AI membuat caption', 'time' => '5 jam lalu', 'detail' => 'Caption siap diposting dengan tone santai'],
            ['label' => 'konten dijadwalkan', 'time' => '1 hari lalu', 'detail' => 'Jadwal posting Bendungan Cijalu'],
            ['label' => 'posting berhasil', 'time' => '2 hari lalu', 'detail' => 'Posting berhasil terbit di Facebook Page'],
        ]);

        $categories = Category::query()->where('is_active', true)->get();

        return view('dashboard', compact('stats', 'upcomingPosts', 'activity', 'categories'));
    }
}
