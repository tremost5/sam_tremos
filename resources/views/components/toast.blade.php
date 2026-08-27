@php
    // Collect known flash keys into a normalized array for JS
    $toasts = [];
    if (session('ai_test_success')) {
        $toasts[] = ['type' => 'success', 'key' => 'ai_test_success', 'text' => session('ai_test_success')];
    }
    if (session('ai_test_error')) {
        $toasts[] = ['type' => 'error', 'key' => 'ai_test_error', 'text' => session('ai_test_error')];
    }
    if (session('meta_test_success')) {
        $toasts[] = ['type' => 'success', 'key' => 'meta_test_success', 'text' => session('meta_test_success')];
    }
    if (session('meta_test_error')) {
        $toasts[] = ['type' => 'error', 'key' => 'meta_test_error', 'text' => session('meta_test_error')];
    }
    if (session('success')) {
        $successKey = session('meta_success') ? 'meta_success' : (session('ai_success') ? 'ai_success' : 'success');
        $toasts[] = ['type' => 'success', 'key' => $successKey, 'text' => session('success')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'key' => 'error', 'text' => session('error')];
    }
    if (session('warning')) {
        $toasts[] = ['type' => 'warning', 'key' => 'warning', 'text' => session('warning')];
    }
    if (session('info')) {
        $toasts[] = ['type' => 'info', 'key' => 'info', 'text' => session('info')];
    }
    if ($errors->any()) {
        $toasts[] = ['type' => 'warning', 'key' => 'validation', 'text' => 'Periksa kembali pengaturan.'];
    }
@endphp

<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col items-end gap-3 pointer-events-none"></div>

<script>
    (function(){
        const DURATION = { success: 4000, error: 6000, warning: 6000, info: 5000 };

        function iconFor(type){
            return {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ',
                loading: '⟳'
            }[type] || '';
        }

        function createToast(type, title, message, opts = {}){
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto max-w-sm w-full rounded-lg border p-3 shadow-lg transform transition-all duration-300 bg-slate-900 border-slate-800 text-slate-200';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px) translateX(8px)';

            const icon = document.createElement('div');
            icon.className = 'inline-flex items-center justify-center mr-2 rounded-full w-7 h-7 flex-shrink-0';
            icon.textContent = iconFor(type);
            if (type === 'success') icon.classList.add('bg-emerald-600');
            if (type === 'error') icon.classList.add('bg-rose-600');
            if (type === 'warning') icon.classList.add('bg-amber-500');
            if (type === 'info') icon.classList.add('bg-sky-500');
            if (type === 'loading') icon.classList.add('bg-slate-600');

            const content = document.createElement('div');
            content.className = 'flex-1';
            const titleEl = document.createElement('div');
            titleEl.className = 'font-semibold text-sm';
            titleEl.textContent = title || '';
            const msgEl = document.createElement('div');
            msgEl.className = 'text-sm text-slate-300';
            msgEl.textContent = message || '';

            const right = document.createElement('div');
            right.className = 'ml-3 flex items-start';
            const closeBtn = document.createElement('button');
            closeBtn.className = 'text-slate-400 hover:text-slate-200 ml-2';
            closeBtn.innerHTML = '&#10005;';
            closeBtn.addEventListener('click', () => removeToast(toast));

            content.appendChild(titleEl);
            content.appendChild(msgEl);

            const inner = document.createElement('div');
            inner.className = 'flex items-start';
            inner.appendChild(icon);
            inner.appendChild(content);
            inner.appendChild(right);
            right.appendChild(closeBtn);

            toast.appendChild(inner);
            container.appendChild(toast);

            // entrance
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0) translateX(0)';
            });

            if (!opts.sticky) {
                const timeout = DURATION[type] || 5000;
                toast._timer = setTimeout(() => removeToast(toast), timeout);
            }

            return toast;
        }

        function removeToast(toast){
            if (!toast) return;
            if (toast._timer) clearTimeout(toast._timer);
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px) translateX(8px)';
            setTimeout(()=>{ if(toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
        }

        // expose global for manual use
        window.__toast = { create: createToast };

        // render initial server flashes
        const initial = @json($toasts);
        initial.forEach(t => {
            // map keys to friendly titles/messages where appropriate
            let title = '';
            let msg = t.text || '';
            if (t.key === 'ai_test_success') { title = '✓ Koneksi AI berhasil'; msg = 'Provider AI dapat diakses dan konfigurasi valid.'; }
            else if (t.key === 'ai_test_error') { title = '✕ Koneksi AI gagal'; msg = 'Periksa API Key, provider, dan model.'; }
            else if (t.key === 'meta_test_success') { title = '✓ Koneksi Meta berhasil'; msg = 'Konfigurasi Meta dapat digunakan untuk OAuth.'; }
            else if (t.key === 'meta_test_error') { title = '✕ Koneksi Meta gagal'; msg = msg || 'Periksa App ID, App Secret, dan Redirect URI.'; }
            else if (t.key === 'ai_success') { title = '✓ Konfigurasi AI berhasil disimpan'; msg = 'Konfigurasi tersimpan dan siap digunakan.'; }
            else if (t.key === 'meta_success') { title = '✓ Konfigurasi Meta berhasil disimpan'; msg = 'Konfigurasi Meta tersimpan dan siap digunakan.'; }
            else if (t.key === 'success') { title = '✓ Pengaturan berhasil disimpan'; msg = msg || 'Perubahan konfigurasi berhasil diterapkan.'; }
            else if (t.key === 'error') { title = '✕ Pengaturan gagal disimpan'; msg = msg || 'Terjadi kesalahan saat menyimpan.'; }
            else if (t.key === 'validation') { title = '⚠ Periksa kembali pengaturan'; }
            else { title = t.key; }

            createToast(t.type, title, msg, { sticky: false });
        });
    })();
</script>
