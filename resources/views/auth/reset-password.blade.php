<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password · SAM TREMOS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>html,body,#app{height:100%;min-height:100vh}</style>
</head>
<body class="antialiased font-sans bg-white text-gray-900">
    <div id="app" class="min-h-screen flex">
        <aside class="hidden lg:block lg:w-7/12 xl:w-8/12 relative" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-b from-sky-900 via-emerald-800 to-green-700"></div>
            <div class="absolute left-12 bottom-20 text-white z-10 max-w-xl">
                <h1 class="text-5xl font-extrabold leading-tight">Konten mancing,<br/>biar AI yang mikirin.</h1>
                <p class="mt-4 text-lg text-white/90 max-w-lg">Masukkan password baru Anda untuk mengamankan akun.</p>
            </div>
        </aside>

        <main class="flex-1 w-full lg:w-5/12 xl:w-4/12 flex items-center justify-center bg-white">
            <div class="w-full px-6 py-12" style="max-width:420px;">
                <h2 class="text-3xl font-semibold">Reset Password</h2>
                <p class="mt-2 text-sm text-gray-600">Masukkan email dan password baru Anda.</p>

                <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required autocomplete="username" value="{{ old('email', $request->email) }}" class="block w-full pl-3 pr-3 h-12 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full pl-3 pr-3 h-12 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full pl-3 pr-3 h-12 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div>
                        <button type="submit" class="w-full h-12 bg-emerald-600 text-white rounded-lg">Reset Password</button>
                    </div>
                </form>

                <div class="mt-10 text-center text-xs text-gray-400">© 2026 Sam Tremos · AI Fishing Content Autopilot</div>
            </div>
        </main>
    </div>
</body>
</html>
