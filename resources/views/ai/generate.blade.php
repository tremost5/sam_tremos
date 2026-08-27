<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Generator | Sam Tremos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
<div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-sky-400">PilotFB</p>
                <h1 class="mt-2 text-3xl font-bold text-white">AI Content Generator</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-100">Dashboard</a>
        </div>

        <form method="POST" action="{{ route('ai.generate.store') }}" class="space-y-6 rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl shadow-slate-950/40">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm text-slate-300">Kategori</label>
                    <select name="category_id" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300">Jumlah konten</label>
                    <input type="number" name="quantity" min="1" max="10" value="3" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300">Tone</label>
                    <select name="tone" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                        <option value="santai">Santai</option>
                        <option value="natural">Natural</option>
                        <option value="engaging">Engaging</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300">Bahasa</label>
                    <select name="language" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-slate-100">
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                <label class="flex items-center gap-3 text-slate-200">
                    <input type="checkbox" name="image_enabled" value="1" checked class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500">
                    Aktifkan gambar AI
                </label>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-5">
                <p class="mb-3 text-sm font-medium text-slate-200">Ide yang bisa dipilih AI</p>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li>• Tips umpan nila</li>
                    <li>• Trik menggunakan tegek</li>
                    <li>• Kenapa ikan tiba-tiba tidak makan?</li>
                    <li>• Pagi atau sore lebih enak mancing?</li>
                    <li>• Cerita pemancing boncos</li>
                    <li>• Fakta ikan mujair</li>
                    <li>• Suasana mancing di bendungan</li>
                    <li>• Humor pemancing</li>
                </ul>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-sky-500 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-sky-400">Generate Konten</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
