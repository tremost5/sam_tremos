<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konten | AI Fishing Content Autopilot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-slate-950 px-6 py-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-sky-400">PilotFB</p>
                    <h1 class="text-2xl font-bold">Manajemen Konten</h1>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-700 px-4 py-2 text-sm">Dashboard</a>
                    <a href="{{ route('posts.create') }}" class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950">Tambah Konten</a>
                </div>
            </div>

            <div class="mb-6 rounded-2xl border border-slate-800 bg-slate-900 p-4">
                <form method="GET" class="grid gap-4 md:grid-cols-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..." class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                    <select name="status" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                        <option value="">Semua status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <select name="category_id" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-slate-100">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-sky-500 px-4 py-2 font-semibold text-slate-950">Filter</button>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-200">Judul</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-200">Kategori</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-200">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-200">Quality</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-200">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                    @forelse($posts as $post)
                        <tr>
                            <td class="px-4 py-3 text-slate-100">{{ $post->title }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $post->category?->name ?? 'Umum' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-800 px-2 py-1 text-xs font-medium text-sky-300">{{ $post->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-300">{{ $post->quality_score ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('posts.show', $post) }}" class="text-sky-400">View</a>
                                    <a href="{{ route('posts.edit', $post) }}" class="text-amber-400">Edit</a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Hapus konten ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada konten.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</body>
</html>
