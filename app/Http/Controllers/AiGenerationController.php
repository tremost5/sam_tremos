<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateContentJob;
use App\Jobs\GenerateImageJob;
use App\Jobs\ValidateContentJob;
use App\Models\Category;
use App\Models\Post;
use App\Services\AIContentEngine;
use App\Services\ContentQualityService;
use App\Services\ContentSimilarityService;
use App\Services\ImageGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AiGenerationController extends Controller
{
    public function create()
    {
        $categories = Category::query()->where('is_active', true)->get();

        return view('ai.generate', compact('categories'));
    }

    public function store(Request $request, AIContentEngine $engine, ContentQualityService $qualityService, ContentSimilarityService $similarityService)
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'tone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:20'],
            'image_enabled' => ['nullable', 'boolean'],
        ]);

        $category = $request->category_id ? Category::find($request->category_id) : null;
        $categoryName = $category?->name ?? 'Fishing Lifestyle';
        $history = Post::query()->where('user_id', Auth::id())->orderByDesc('created_at')->limit(20)->pluck('title')->all();

        if (! $engine->isProviderConfigured()) {
            return redirect()->back()->with('error', 'AI provider belum dikonfigurasi. Set AI_API_KEY dan AI_TEXT_MODEL terlebih dahulu.');
        }

        $jobsQueued = 0;
        for ($i = 0; $i < (int) $request->quantity; $i++) {
            $context = [
                'category' => $categoryName,
                'category_id' => $category?->id,
                'language' => $request->language ?: 'id',
                'tone' => $request->tone ?: 'santai',
                'history' => $history,
                'image_enabled' => (bool) $request->boolean('image_enabled'),
            ];

            \App\Jobs\GenerateContentJob::dispatch(Auth::id(), $context);
            $jobsQueued++;
        }

        return redirect()->route('posts.index')->with('success', "AI berhasil mengantri {$jobsQueued} tugas pembuatan konten untuk {$categoryName}. Periksa antrian pekerjaan.");
    }
}
