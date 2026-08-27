<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('category')->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('idea', 'like', '%'.$request->search.'%')
                    ->orWhere('caption', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $posts = $query->latest()->paginate(10);
        $categories = Category::query()->where('is_active', true)->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::query()->where('is_active', true)->get();

        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'idea' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'caption' => ['nullable', 'string'],
            'hashtags' => ['nullable', 'string'],
            'engagement_question' => ['nullable', 'string'],
            'image_prompt' => ['nullable', 'string'],
            'quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,generating,ready,scheduled,publishing,published,failed,cancelled'],
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'] ?? 'Konten AI '.now()->format('d/m/Y'),
            'idea' => $validated['idea'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'hashtags' => $validated['hashtags'] ?? null,
            'engagement_question' => $validated['engagement_question'] ?? null,
            'image_prompt' => $validated['image_prompt'] ?? null,
            'quality_score' => $validated['quality_score'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => $validated['status'],
            'ai_generated' => true,
        ]);

        return redirect()->route('posts.index')->with('success', 'Konten berhasil disimpan.');
    }

    public function show(Post $post)
    {
        $this->authorizePost($post);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorizePost($post);
        $categories = Category::query()->where('is_active', true)->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizePost($post);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'idea' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'caption' => ['nullable', 'string'],
            'hashtags' => ['nullable', 'string'],
            'engagement_question' => ['nullable', 'string'],
            'image_prompt' => ['nullable', 'string'],
            'quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,generating,ready,scheduled,publishing,published,failed,cancelled'],
        ]);

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $this->authorizePost($post);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Konten berhasil dihapus.');
    }

    public function regenerate(Post $post)
    {
        $this->authorizePost($post);
        $post->update(['status' => 'generating']);

        return redirect()->route('posts.show', $post)->with('success', 'Konten sedang diregenerasi.');
    }

    public function approve(Post $post)
    {
        $this->authorizePost($post);
        $post->update(['status' => 'ready']);

        return redirect()->route('posts.show', $post)->with('success', 'Konten disetujui dan siap dipublish.');
    }

    public function schedule(Post $post)
    {
        $this->authorizePost($post);
        $scheduledAt = $post->scheduled_at?->toDateTimeString() ?? now()->addHours(6)->toDateTimeString();

        \App\Jobs\SchedulePostJob::dispatch($post->id, $scheduledAt);

        return redirect()->route('posts.show', $post)->with('success', 'Penjadwalan konten telah dikirim ke antrian.');
    }

    public function cancel(Post $post)
    {
        $this->authorizePost($post);
        $post->update(['status' => 'cancelled']);

        return redirect()->route('posts.index')->with('success', 'Jadwal konten dibatalkan.');
    }

    protected function authorizePost(Post $post): void
    {
        abort_unless($post->user_id === Auth::id(), 403);
    }
}
