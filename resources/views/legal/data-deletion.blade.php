<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Penghapusan Data | Sam Tremos PilotFB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 px-6 py-10 text-slate-100">
    <main class="mx-auto max-w-3xl space-y-6">
        <h1 class="text-3xl font-bold">Penghapusan Data</h1>
        <p class="text-slate-300">Pengguna Sam Tremos PilotFB dapat meminta penghapusan data aplikasi dan memutus koneksi Facebook.</p>
        <section><h2 class="text-xl font-semibold">Cara menghapus data</h2><ol class="mt-2 list-decimal space-y-2 pl-6 text-slate-300"><li>Masuk ke aplikasi dan buka halaman Profile untuk menghapus akun, atau halaman Facebook untuk memutus koneksi.</li><li>Untuk permintaan manual, hubungi pemilik aplikasi melalui alamat kontak bisnis Sam Tremos dan sertakan email akun yang digunakan.</li><li>Setelah permintaan diverifikasi, data akun, konfigurasi, konten, koneksi Facebook, dan token terkait akan dihapus dari penyimpanan aplikasi sesuai kewajiban hukum.</li></ol></section>
        <section><h2 class="text-xl font-semibold">Permintaan dari Facebook</h2><p class="mt-2 text-slate-300">Jika Facebook mengirimkan permintaan penghapusan data, pemilik aplikasi akan memprosesnya berdasarkan identitas dan data callback yang tersedia tanpa menampilkan access token kepada pengguna.</p></section>
        <a class="text-sky-400 underline" href="{{ route('privacy-policy') }}">Lihat Kebijakan Privasi</a>
    </main>
</body>
</html>
