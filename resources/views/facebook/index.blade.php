<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook | Sam Tremos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
@include('components.toast')
<div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-sky-400">PilotFB</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Facebook Connection</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-sm">Dashboard</a>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            @if(!$metaConfigured)
                <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-200">
                    Meta API belum dikonfigurasi.
                </div>
            @else
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-400">Status</p>
                        <p class="mt-2 text-xl font-semibold text-white">{{ $account ? 'Connected' : 'Disconnected' }}</p>
                    </div>
                    @if($account)
                        <form action="{{ route('facebook.disconnect') }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-200">Disconnect</button>
                        </form>
                    @else
                        <a href="{{ route('facebook.connect') }}" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950">Connect Facebook</a>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
            <h2 class="mb-4 text-xl font-semibold text-white">Pages</h2>
            @forelse($pages as $page)
                <div class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950 p-4">
                    <div>
                        <p class="font-medium text-slate-100">{{ $page->name }}</p>
                        <p class="text-sm text-slate-400">{{ $page->facebook_id }}</p>
                    </div>
                    @if($page->selected)
                        <span class="rounded-full bg-emerald-900/40 px-3 py-1 text-xs uppercase tracking-[0.2em] text-emerald-300">Selected</span>
                    @else
                        <form method="POST" action="{{ route('facebook.pages.select', $page->id) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-sky-500 px-3 py-2 text-xs font-semibold text-slate-950">Pilih Page</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-700 bg-slate-950 p-8 text-center text-slate-400">
                    Belum ada page yang tersambung.
                </div>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
