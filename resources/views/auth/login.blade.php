<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SAM TREMOS · Masuk</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html,body,#app{height:100%;min-height:100vh}
        /* Desktop split 55/45 */
        @media (min-width:1024px) {
            .left-panel{width:55%}
            .right-panel{width:45%}
        }
        .hero-svg { display:block; width:100%; height:100%; object-fit:cover }
    </style>
</head>
<body class="antialiased font-sans bg-white text-gray-900">
    <div id="app" class="min-h-screen flex">
        <!-- LEFT HERO PANEL -->
        <aside class="hidden lg:block left-panel relative h-screen overflow-hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-b from-sky-900 via-emerald-800 to-green-700"></div>
            <div class="absolute inset-0 opacity-80">
                <!-- Stronger fishing hero SVG (no external assets) -->
                <svg class="hero-svg" viewBox="0 0 1600 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="sky" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#ffd59a" stop-opacity="0.95"/><stop offset="40%" stop-color="#ffb66b" stop-opacity="0.85"/><stop offset="100%" stop-color="#0b6b4f" stop-opacity="0.9"/></linearGradient>
                        <linearGradient id="water" x1="0" x2="1"><stop offset="0%" stop-color="#0b9bd6"/><stop offset="100%" stop-color="#044f46"/></linearGradient>
                        <filter id="soft" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="18"/></filter>
                    </defs>
                    <rect width="1600" height="900" fill="url(#sky)"/>

                    <!-- distant trees silhouette -->
                    <g transform="translate(0,120)" fill="#052230" opacity="0.85">
                        <rect x="0" y="520" width="1600" height="400"/>
                        <path d="M120 540 L160 480 L200 540 Z M260 540 L300 470 L340 540 Z M420 540 L460 500 L500 540 Z"/>
                    </g>

                    <!-- lake -->
                    <g transform="translate(0,320)">
                        <rect x="0" y="220" width="1600" height="420" fill="url(#water)"/>
                        <!-- soft waves -->
                        <g fill="none" stroke="#ffffff22" stroke-width="2" opacity="0.6">
                            <path d="M0 360 C160 330 320 370 480 350 C640 330 800 370 960 350 C1120 330 1280 370 1440 350 C1600 330 1760 370 1920 350"/>
                        </g>
                    </g>

                    <!-- fisherman silhouette on shore -->
                    <g transform="translate(220,300) scale(1.2)">
                        <path d="M220 280 C200 260 180 240 160 220 L150 230 L160 240 C140 260 130 280 120 300 L140 310 C160 330 190 340 220 340 L260 340 C300 340 320 320 340 300 L360 280 C340 260 320 240 300 220 L280 200" fill="#031b1a"/>
                        <!-- rod -->
                        <path d="M300 120 L520 40" stroke="#000" stroke-width="6" stroke-linecap="round"/>
                        <line x1="520" y1="40" x2="540" y2="80" stroke="#ffffff55" stroke-width="1.5"/>
                        <!-- line to water -->
                        <path d="M540 80 C560 140 600 220 640 300" stroke="#ffffff88" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    </g>

                    <!-- subtle vignette -->
                    <rect width="1600" height="900" fill="black" opacity="0.06"/>
                </svg>
            </div>

            <div class="absolute left-12 bottom-20 text-white z-20 max-w-xl">
                <h1 class="text-5xl leading-tight font-extrabold">Konten mancing,<br/>biar AI yang mikirin.</h1>
                <p class="mt-4 text-lg text-white/90 max-w-lg">Generate ide, caption, gambar, jadwal, dan siapkan konten Facebook secara otomatis.</p>
                <div class="mt-6 flex gap-3">
                    <span class="px-3 py-1 bg-white/10 rounded-full text-sm font-medium">AI Content</span>
                    <span class="px-3 py-1 bg-white/10 rounded-full text-sm font-medium">Auto Schedule</span>
                    <span class="px-3 py-1 bg-white/10 rounded-full text-sm font-medium">Meta Ready</span>
                </div>
            </div>

            <div class="absolute left-12 top-12 text-white z-30">
                <div class="text-sm uppercase tracking-widest">SAM TREMOS</div>
                <div class="text-xs text-white/80">AI FISHING CONTENT AUTOPILOT</div>
            </div>
        </aside>

        <!-- RIGHT LOGIN PANEL -->
        <main class="right-panel flex-1 w-full flex items-center justify-center bg-gray-50 h-screen overflow-y-auto">
            <div class="w-full px-6 py-12" style="max-width:420px;">
                <div class="mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 flex items-center justify-center rounded-full bg-emerald-600 text-white">
                            <!-- simple fish logo -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M3 12c2-2 4-4 8-4s6 2 8 4c-2 2-4 4-8 4s-6-2-8-4z" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-bold">SAM TREMOS</div>
                            <div class="text-xs text-gray-500">AI FISHING CONTENT AUTOPILOT</div>
                        </div>
                    </div>
                </div>

                <h2 class="text-3xl font-semibold">Selamat datang kembali</h2>
                <p class="mt-2 text-sm text-gray-600">Masuk untuk mengelola konten dan autopilot Facebook kamu.</p>

                @if ($errors->any())
                    <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded text-red-700">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" class="mt-6 space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-4 4m4-4l-4-4"/>
                                </svg>
                            </span>
                            <input id="email" name="email" type="email" required autocomplete="username" value="{{ old('email') }}" class="block w-full pl-10 pr-3 h-[52px] text-[15px] rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20v-2a4 4 0 014-4h8a4 4 0 014 4v2"/>
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" required autocomplete="current-password" class="block w-full pl-10 pr-20 h-[52px] text-[15px] rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent" />
                            <button type="button" id="togglePassword" aria-label="Tampilkan password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-500">Tampilkan</button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center text-gray-600">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                            <span class="ml-2">Ingat saya</span>
                        </label>
                        <div>
                            @if (Route::has('password.request'))
                                <a class="text-emerald-600 font-medium hover:underline" href="{{ route('password.request') }}">Lupa password?</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <button type="submit" id="submitBtn" class="w-full h-[52px] flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <span id="btnText">Masuk</span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm text-gray-600">
                    @if (Route::has('register'))
                        Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-600 font-medium hover:underline">Daftar</a>
                    @endif
                </div>

                <div class="mt-10 text-center text-xs text-gray-400">© 2026 Sam Tremos · AI Fishing Content Autopilot</div>
            </div>
        </main>
    </div>

    <script>
        (function(){
            var form = document.getElementById('loginForm');
            var btn = document.getElementById('submitBtn');
            var btnText = document.getElementById('btnText');
            var toggle = document.getElementById('togglePassword');
            var pwd = document.getElementById('password');

            if (toggle && pwd) {
                toggle.addEventListener('click', function(){
                    if (pwd.type === 'password') { pwd.type = 'text'; toggle.textContent = 'Sembunyikan'; toggle.setAttribute('aria-pressed','true'); }
                    else { pwd.type = 'password'; toggle.textContent = 'Tampilkan'; toggle.setAttribute('aria-pressed','false'); }
                });
            }

            if (form && btn) {
                form.addEventListener('submit', function(e){
                    btn.disabled = true;
                    btn.setAttribute('aria-disabled', 'true');
                    btnText.textContent = 'Memproses...';
                });
            }
        })();
    </script>
</body>
</html>
