<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} | AI Fishing Content Autopilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-4xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-sky-400">PilotFB</p>
                <h1 class="text-2xl font-bold">Detail Konten</h1>
            </div>
            <a href="{{ route('posts.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm">Kembali</a>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-50">{{ $post->title }}</h2>
                <span class="rounded-full bg-sky-500/10 px-3 py-1 text-xs font-medium text-sky-300">{{ $post->status }}</span>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kategori</p>
                    <p class="mt-2 text-lg text-slate-100">{{ $post->category?->name ?? 'Umum' }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-950 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Quality Score</p>
                    <p class="mt-2 text-lg text-slate-100">{{ $post->quality_score ?? 'Belum diukur' }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-5 text-slate-300">
                <div>
                    <p class="mb-2 text-xs uppercase tracking-[0.2em] text-slate-400">Caption</p>
                    <p class="leading-7">{{ $post->caption ?? 'Belum ada caption.' }}</p>
                </div>
                <div>
                    <p class="mb-2 text-xs uppercase tracking-[0.2em] text-slate-400">Hashtags</p>
                    <p>{{ $post->hashtags ?? '-' }}</p>
                </div>
                <div>
                    <p class="mb-2 text-xs uppercase tracking-[0.2em] text-slate-400">Image Prompt</p>
                    <p>{{ $post->image_prompt ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
