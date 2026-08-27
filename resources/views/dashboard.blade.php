<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sam Tremos | AI Fishing Content Autopilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <header class="mb-8 flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-2xl shadow-slate-950/40 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.28em] text-sky-400">PilotFB</p>
                    <h1 class="mt-2 text-3xl font-bold text-white">Sam Tremos</h1>
                    <p class="mt-1 text-sm text-slate-400">AI Fishing Content Autopilot</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('posts.index') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-sm font-medium text-slate-100 hover:border-sky-500">Konten</a>
                    <a href="{{ route('ai.generate') }}" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-sky-400">Generate AI</a>
                    <a href="{{ route('settings') }}" title="Pengaturan" class="rounded-xl border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-100 hover:border-sky-500">⚙ Pengaturan</a>
                </div>
            </header>

            <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                @php
                    $stats = $stats ?? [];
                @endphp
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Konten</p>
                    <p class="mt-4 text-3xl font-bold text-white">{{ $stats['total_posts'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Draft</p>
                    <p class="mt-4 text-3xl font-bold text-amber-300">{{ $stats['draft'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Terjadwal</p>
                    <p class="mt-4 text-3xl font-bold text-sky-300">{{ $stats['scheduled'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Published</p>
                    <p class="mt-4 text-3xl font-bold text-emerald-300">{{ $stats['published'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Failed</p>
                    <p class="mt-4 text-3xl font-bold text-rose-300">{{ $stats['failed'] ?? 0 }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Autopilot</p>
                    <p class="mt-4 text-xl font-bold text-violet-300">{{ $stats['autopilot_status'] ?? 'Manual' }}</p>
                    <div class="mt-2 text-sm text-slate-400">
                        <span class="mr-2">AI: <strong class="text-white">{{ $stats['ai_status'] ?? 'Unknown' }}</strong></span>
                        <span class="mr-2">Autopilot: <strong class="text-white">{{ $stats['autopilot_active'] ? 'Active' : 'Off' }}</strong></span>
                        <span>Facebook: <strong class="text-white">{{ $stats['facebook_status'] ?? 'Unknown' }}</strong></span>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[1.5fr,1fr]">
                <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-white">Upcoming Posts</h2>
                        <span class="rounded-full border border-sky-500/30 bg-sky-500/10 px-2.5 py-1 text-xs font-medium text-sky-300">Inventory {{ $stats['content_inventory'] ?? 0 }}</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($upcomingPosts as $post)
                            <div class="flex gap-4 rounded-2xl border border-slate-800 bg-slate-950 p-3">
                                <div class="h-20 w-20 overflow-hidden rounded-xl bg-gradient-to-br from-sky-500 via-blue-500 to-violet-500 p-2">
                                    <div class="flex h-full items-end rounded-lg bg-slate-900/60 p-2 text-[10px] font-semibold text-slate-100">
                                        {{ strtoupper(Str::limit($post->category?->name ?? 'Mancing', 10)) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-white">{{ $post->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-400">{{ $post->category?->name ?? 'Umum' }}</p>
                                    <div class="mt-2 flex items-center justify-between gap-2 text-sm text-slate-300">
                                        <span>{{ $post->scheduled_at?->format('d M Y, H:i') ?? 'Belum dijadwalkan' }}</span>
                                        <span class="rounded-full bg-slate-800 px-2 py-1 text-xs uppercase tracking-[0.2em] text-sky-300">{{ $post->status }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-950 p-6 text-center text-slate-400">
                                Belum ada konten yang dijadwalkan.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
                    <h2 class="mb-5 text-xl font-semibold text-white">Activity</h2>
                    <div class="space-y-4">
                        @foreach($activity as $item)
                            <div class="flex gap-3 rounded-xl border border-slate-800 bg-slate-950 p-3">
                                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-sky-400"></div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-medium text-slate-100">{{ $item['label'] }}</p>
                                        <span class="text-[11px] text-slate-400">{{ $item['time'] }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-400">{{ $item['detail'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
