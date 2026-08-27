<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Konten | AI Fishing Content Autopilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-3xl px-6 py-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-sky-400">PilotFB</p>
                <h1 class="text-2xl font-bold">Tambah Konten Baru</h1>
            </div>
            <a href="{{ route('posts.index') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm">Kembali</a>
        </div>

        <form method="POST" action="{{ route('posts.store') }}" class="space-y-5 rounded-2xl border border-slate-800 bg-slate-900 p-6">
            @csrf
            <div>
                <label class="mb-2 block text-sm text-slate-300">Judul</label>
                <input type="text" name="title" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100" required>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Caption</label>
                <textarea name="caption" rows="5" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Hashtags</label>
                <input type="text" name="hashtags" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Image Prompt</label>
                <textarea name="image_prompt" rows="3" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                    <option value="draft">Draft</option>
                    <option value="ready">Ready</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-sky-500 px-5 py-3 font-semibold text-slate-950">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
