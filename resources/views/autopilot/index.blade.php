<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autopilot | Sam Tremos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
<div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-sky-400">PilotFB</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Autopilot</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-sm">Dashboard</a>
        </div>

        <form method="POST" action="{{ route('autopilot.update') }}" class="space-y-6 rounded-2xl border border-slate-800 bg-slate-900 p-6">
            @csrf
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Status</span>
                    <select name="enabled" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                        <option value="1" {{ $setting->enabled ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ ! $setting->enabled ? 'selected' : '' }}>Disabled</option>
                    </select>
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Mode</span>
                    <select name="mode" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                        <option value="manual" {{ $setting->mode === 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="semi-auto" {{ $setting->mode === 'semi-auto' ? 'selected' : '' }}>Semi Auto</option>
                        <option value="full-autopilot" {{ $setting->mode === 'full-autopilot' ? 'selected' : '' }}>Full Autopilot</option>
                    </select>
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Posts per Day</span>
                    <input type="number" name="posts_per_day" value="{{ $setting->posts_per_day ?? 2 }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Timezone</span>
                    <input type="text" name="timezone" value="{{ $setting->timezone ?? 'Asia/Jakarta' }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Minimum Inventory</span>
                    <input type="number" name="minimum_inventory" value="{{ $setting->minimum_inventory ?? 5 }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Target Inventory</span>
                    <input type="number" name="target_inventory" value="{{ $setting->target_inventory ?? 14 }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Minimum Quality</span>
                    <input type="number" name="minimum_quality_score" value="{{ $setting->minimum_quality_score ?? 75 }}" min="0" max="100" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                </label>
                <label class="space-y-2">
                    <span class="text-sm text-slate-300">Posting Window</span>
                    <div class="flex gap-2">
                        <input type="time" name="start_time" value="{{ $setting->start_time ?? '08:00' }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                        <input type="time" name="end_time" value="{{ $setting->end_time ?? '18:00' }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3">
                    </div>
                </label>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950 p-3">
                    <input type="checkbox" name="image_enabled" value="1" {{ $setting->image_enabled ? 'checked' : '' }}>
                    <span>Image Generation</span>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950 p-3">
                    <input type="checkbox" name="require_approval" value="1" {{ $setting->require_approval ? 'checked' : '' }}>
                    <span>Require Approval</span>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-950 p-3">
                    <input type="checkbox" name="auto_publish" value="1" {{ $setting->auto_publish ? 'checked' : '' }}>
                    <span>Auto Publish</span>
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-3 text-sm font-semibold text-slate-950">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
