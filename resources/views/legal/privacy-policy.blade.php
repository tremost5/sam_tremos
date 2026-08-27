<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi | Sam Tremos PilotFB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 px-6 py-10 text-slate-100">
    <main class="mx-auto max-w-3xl space-y-6">
        <h1 class="text-3xl font-bold">Kebijakan Privasi Sam Tremos PilotFB</h1>
        <p class="text-slate-300">PilotFB membantu pengguna Sam Tremos membuat, mengelola, menjadwalkan, dan mempublikasikan konten fishing ke Facebook Page yang mereka hubungkan.</p>
        <section><h2 class="text-xl font-semibold">Data yang diproses</h2><p class="mt-2 text-slate-300">Aplikasi dapat memproses nama, email, konfigurasi AI, Meta App ID, metadata Facebook Page, access token terenkripsi, konten, jadwal, dan status publikasi.</p></section>
        <section><h2 class="text-xl font-semibold">Facebook dan Meta</h2><p class="mt-2 text-slate-300">Dengan izin pengguna, PilotFB menggunakan Facebook Login dan Graph API untuk mengambil Page yang tersedia dan melakukan publikasi sesuai pengaturan pengguna. Aplikasi tidak menjual data pengguna.</p></section>
        <section><h2 class="text-xl font-semibold">Penyimpanan dan keamanan</h2><p class="mt-2 text-slate-300">Credential AI, Meta App Secret, dan access token disimpan server-side dalam bentuk terenkripsi bila disimpan oleh aplikasi. Credential tidak ditampilkan kembali di halaman atau log aplikasi.</p></section>
        <section><h2 class="text-xl font-semibold">Hak pengguna</h2><p class="mt-2 text-slate-300">Pengguna dapat memutuskan koneksi Facebook, mengganti konfigurasi, dan meminta penghapusan data. Lihat <a class="text-sky-400 underline" href="{{ route('data-deletion') }}">halaman penghapusan data</a>.</p></section>
        <section><h2 class="text-xl font-semibold">Kontak</h2><p class="mt-2 text-slate-300">Untuk pertanyaan privasi atau permintaan data, hubungi pemilik aplikasi melalui alamat kontak yang terdaftar pada akun bisnis Sam Tremos.</p></section>
        <a class="text-sky-400 underline" href="{{ url('/') }}">Kembali ke aplikasi</a>
    </main>
</body>
</html>
