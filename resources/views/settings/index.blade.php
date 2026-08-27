@extends('layouts.app')

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Pengaturan</h1>
                <p class="mt-1 text-sm text-slate-400">Kelola AI, autopilot, Facebook, dan konfigurasi aplikasi.</p>
            </div>
        </header>

        {{-- Toast container & server flash rendering --}}
        @include('components.toast')

        <div class="grid gap-4 md:grid-cols-2">
            <!-- AI Provider Configuration -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 md:col-span-2">
                <h3 class="text-lg font-semibold text-white">AI Provider Configuration</h3>
                <form method="POST" action="{{ route('settings.ai.update') }}" class="mt-3">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm text-slate-400">Provider</span>
                            <input name="provider" type="text" value="{{ $aiProvider ?? 'openai' }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Text Model</span>
                            <input name="text_model" type="text" value="{{ $aiTextModel ?? '' }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Image Model</span>
                            <input name="image_model" type="text" value="{{ $aiImageModel ?? '' }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block col-span-2">
                            <span class="text-sm text-slate-400">API Key</span>
                            <input name="api_key" type="password" placeholder="Masukkan API key baru" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">API Key: <strong class="text-white">{{ $aiHasKey ? 'Configured' : 'Not configured' }}</strong></p>
                        </label>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950">Simpan Konfigurasi AI</button>
                        <button id="test-ai-btn" type="button" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950">Test AI Connection</button>
                    </div>
                </form>

            <!-- Facebook / Meta Configuration -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 md:col-span-2">
                <h3 class="text-lg font-semibold text-white">Facebook / Meta Configuration</h3>
                <form method="POST" action="{{ route('settings.meta.update') }}" class="mt-3">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm text-slate-400">Meta App ID</span>
                            <input name="app_id" type="text" value="{{ $metaAppId ?? '' }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block col-span-2">
                            <span class="text-sm text-slate-400">Meta App Secret</span>
                            <input name="app_secret" type="password" placeholder="Masukkan App Secret baru" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                            <p class="mt-1 text-xs text-slate-500">App Secret: <strong class="text-white">{{ $metaHasSecret ? 'Configured' : 'Not configured' }}</strong></p>
                        </label>

                        <label class="block col-span-2">
                            <span class="text-sm text-slate-400">Redirect URI</span>
                            <input name="redirect_uri" type="text" value="{{ $metaRedirect ?? route('facebook.callback') }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950">Simpan Konfigurasi Meta</button>
                        <button id="test-meta-btn" type="button" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950">Test Meta Connection</button>
                    </div>
                </form>

                <script>
                    (function(){
                        const testBtn = document.getElementById('test-meta-btn');
                        if (!testBtn) return;
                        const metaForm = testBtn.closest('form');
                        testBtn.addEventListener('click', async function(e){
                            const originalText = testBtn.textContent;
                            testBtn.disabled = true;
                            testBtn.textContent = 'Menguji...';

                            const loading = window.__toast.create('loading', '⟳ Menguji koneksi Meta...', 'Mohon tunggu — sedang memvalidasi konfigurasi.', { sticky: true });

                            try {
                                const formData = new FormData(metaForm);
                                const resp = await fetch("{{ route('settings.meta.test') }}", {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' },
                                    body: formData
                                });

                                loading && loading.parentNode && loading.parentNode.removeChild(loading);

                                if (resp.ok) {
                                    window.__toast.create('success', '✓ Koneksi Meta berhasil', 'Provider Meta dapat diakses dan konfigurasi valid.');
                                } else if (resp.status === 422) {
                                    window.__toast.create('error', '✕ Koneksi Meta gagal', 'Meta App belum dikonfigurasi.');
                                } else {
                                    window.__toast.create('error', '✕ Koneksi Meta gagal', 'Periksa App ID, App Secret, dan Redirect URI.');
                                }
                            } catch (err) {
                                loading && loading.parentNode && loading.parentNode.removeChild(loading);
                                window.__toast.create('error', '✕ Koneksi Meta gagal', 'Periksa App ID, App Secret, dan Redirect URI.');
                            } finally {
                                testBtn.disabled = false;
                                testBtn.textContent = originalText;
                            }
                        });
                    })();
                </script>
            </div>

                <script>
                    (function(){
                        const testBtn = document.getElementById('test-ai-btn');
                        if (!testBtn) return;
                        const aiForm = testBtn.closest('form');
                        testBtn.addEventListener('click', async function(e){
                            // disable and set loading state
                            const originalText = testBtn.textContent;
                            testBtn.disabled = true;
                            testBtn.textContent = 'Menguji...';

                            // show loading toast (sticky)
                            const loading = window.__toast.create('loading', '⟳ Menguji koneksi AI...', 'Mohon tunggu — sedang menghubungi provider.', { sticky: true });

                            try {
                                const formData = new FormData(aiForm);
                                // Ensure Accept JSON so controller returns JSON
                                const resp = await fetch("{{ route('settings.ai.test') }}", {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' },
                                    body: formData
                                });

                                // remove loading toast
                                loading && loading.parentNode && loading.parentNode.removeChild(loading);

                                if (resp.ok) {
                                    const j = await resp.json();
                                    window.__toast.create('success', '✓ Koneksi AI berhasil', 'Provider AI dapat diakses dan konfigurasi valid.');
                                } else if (resp.status === 422) {
                                    const j = await resp.json();
                                    window.__toast.create('error', '✕ Koneksi AI gagal', 'API Key belum dikonfigurasi.');
                                } else {
                                    window.__toast.create('error', '✕ Koneksi AI gagal', 'Periksa API Key, provider, dan model.');
                                }
                            } catch (err) {
                                // remove loading toast
                                loading && loading.parentNode && loading.parentNode.removeChild(loading);
                                window.__toast.create('error', '✕ Koneksi AI gagal', 'Periksa API Key, provider, dan model.');
                            } finally {
                                testBtn.disabled = false;
                                testBtn.textContent = originalText;
                            }
                        });
                    })();
                </script>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="text-lg font-semibold text-white">AI Provider</h3>
                <p class="mt-2 text-sm text-slate-400">Status Text: <strong class="text-white">{{ $aiStatus }}</strong></p>
                <p class="mt-1 text-sm text-slate-400">Provider: OpenAI-compatible</p>
                <p class="mt-1 text-sm text-slate-400">Text Model: <strong class="text-white">{{ $aiTextModel ?? '—' }}</strong></p>
                <p class="mt-1 text-sm text-slate-400">Image Model: <strong class="text-white">{{ $aiImageModel ?? '—' }}</strong></p>
                <p class="mt-3 text-sm text-amber-300">API Key: <span class="text-slate-300">••••••••••••</span></p>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="text-lg font-semibold text-white">Facebook / Meta</h3>
                <p class="mt-2 text-sm text-slate-400">Meta Configuration: <strong class="text-white">{{ $metaAppId ? '✓ Configured' : '✕ Not configured' }}</strong></p>
                <p class="mt-1 text-sm text-slate-400">Facebook: <strong class="text-white">{{ $facebookStatus }}</strong></p>
                <p class="mt-1 text-sm text-slate-400">Page: <strong class="text-white">{{ $selectedPageName ?? 'Belum ada Page yang dipilih' }}</strong></p>
                <p class="mt-3 text-sm">
                    @if($metaAppId)
                        <a href="{{ route('facebook.index') }}" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950">Hubungkan Facebook</a>
                    @else
                        <button disabled class="opacity-60 cursor-not-allowed rounded-xl bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-400">Hubungkan Facebook (Meta belum dikonfigurasi)</button>
                    @endif
                </p>
            </div>

            <div class="md:col-span-2 rounded-2xl border border-slate-800 bg-slate-900 p-6">
                <h3 class="text-lg font-semibold text-white">Autopilot</h3>
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm text-slate-400">Enabled</span>
                            <select name="enabled" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100">
                                <option value="0" {{ $autopilot->enabled ? '' : 'selected' }}>Off</option>
                                <option value="1" {{ $autopilot->enabled ? 'selected' : '' }}>On</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Mode</span>
                            <select name="mode" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100">
                                <option value="manual" {{ $autopilot->mode === 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="semi" {{ $autopilot->mode === 'semi' ? 'selected' : '' }}>Semi Auto</option>
                                <option value="full" {{ $autopilot->mode === 'full' ? 'selected' : '' }}>Full Autopilot</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Posts per day</span>
                            <input type="number" name="posts_per_day" value="{{ $autopilot->posts_per_day }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Timezone</span>
                            <input type="text" name="timezone" value="{{ $autopilot->timezone }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Language</span>
                            <input type="text" name="language" value="{{ $autopilot->language }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Tone</span>
                            <input type="text" name="tone" value="{{ $autopilot->tone }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Image Enabled</span>
                            <select name="image_enabled" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100">
                                <option value="0" {{ $autopilot->image_enabled ? '' : 'selected' }}>Off</option>
                                <option value="1" {{ $autopilot->image_enabled ? 'selected' : '' }}>On</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Auto Publish</span>
                            <select name="auto_publish" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100">
                                <option value="0" {{ $autopilot->auto_publish ? '' : 'selected' }}>Off</option>
                                <option value="1" {{ $autopilot->auto_publish ? 'selected' : '' }}>On</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Require Approval</span>
                            <select name="require_approval" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100">
                                <option value="0" {{ $autopilot->require_approval ? '' : 'selected' }}>No</option>
                                <option value="1" {{ $autopilot->require_approval ? 'selected' : '' }}>Yes</option>
                            </select>
                        </label>

                        <label class="block col-span-2">
                            <span class="text-sm text-slate-400">Categories (comma separated)</span>
                            <input type="text" name="categories" value="{{ implode(',', $autopilot->categories ?? []) }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Minimum quality score</span>
                            <input type="number" name="minimum_quality_score" value="{{ $autopilot->minimum_quality_score }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Minimum inventory</span>
                            <input type="number" name="minimum_inventory" value="{{ $autopilot->minimum_inventory }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                        <label class="block">
                            <span class="text-sm text-slate-400">Target inventory</span>
                            <input type="number" name="target_inventory" value="{{ $autopilot->target_inventory }}" class="mt-1 block w-full rounded-lg bg-slate-800 text-slate-100" />
                        </label>

                    </div>

                    <div class="mt-4">
                        <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
