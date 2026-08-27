<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email · SAM TREMOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>html,body,#app{height:100%;min-height:100vh}</style>
</head>
<body class="antialiased font-sans bg-white text-gray-900">
    <div id="app" class="min-h-screen flex">
        <aside class="hidden lg:block lg:w-7/12 xl:w-8/12 relative" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-b from-sky-900 via-emerald-800 to-green-700"></div>
            <div class="absolute left-12 bottom-20 text-white z-10 max-w-xl">
                <h1 class="text-5xl font-extrabold leading-tight">Konten mancing,<br/>biar AI yang mikirin.</h1>
            </div>
        </aside>

        <main class="flex-1 w-full lg:w-5/12 xl:w-4/12 flex items-center justify-center bg-white">
            <div class="w-full px-6 py-12" style="max-width:420px;">
                <h2 class="text-3xl font-semibold">Verifikasi Email</h2>
                <p class="mt-2 text-sm text-gray-600">Sebelum mulai, mohon verifikasi alamat email Anda. Jika belum menerima, kami dapat mengirim ulang.</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600 mt-4">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-6 flex flex-col gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full h-12 bg-emerald-600 text-white rounded-lg">Kirim ulang email verifikasi</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full h-12 border border-gray-200 rounded-lg">Keluar</button>
                    </form>
                </div>

                <div class="mt-10 text-center text-xs text-gray-400">© 2026 Sam Tremos · AI Fishing Content Autopilot</div>
            </div>
        </main>
    </div>
</body>
</html>
