<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar | Sam Tremos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
<div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-sky-400">PilotFB</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Calendar</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-sm">Dashboard</a>
        </div>

        <div class="space-y-4 rounded-2xl border border-slate-800 bg-slate-900 p-5">
            @forelse($posts as $post)
                <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-950 p-4">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $post->title }}</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $post->scheduled_at?->format('d M Y H:i') ?? 'Belum dijadwalkan' }} · {{ $post->status }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('posts.show', $post) }}" class="rounded-lg border border-slate-700 px-3 py-2 text-sm">View</a>
                        <a href="{{ route('posts.edit', $post) }}" class="rounded-lg bg-sky-500 px-3 py-2 text-sm font-semibold text-slate-950">Edit</a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-700 bg-slate-950 p-8 text-center text-slate-400">
                    Belum ada konten yang dijadwalkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
