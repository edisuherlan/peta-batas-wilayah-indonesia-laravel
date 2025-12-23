<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang Aplikasi - Peta Batas Wilayah Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 min-h-screen">
    <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-40 border-b border-pink-100">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-pink-300 to-purple-400 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-pink-400 via-purple-500 to-indigo-500 bg-clip-text text-transparent">
                    Peta Batas Wilayah Indonesia
                </span>
            </a>
            <nav class="hidden md:flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-500 transition-colors">Beranda</a>
                <a href="{{ route('tentang') }}" class="text-purple-600 font-semibold">Tentang</a>
                <a href="{{ route('kontak') }}" class="text-gray-600 hover:text-purple-500 transition-colors">Kontak</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-10 max-w-4xl">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-pink-100 p-8 md:p-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Tentang Aplikasi</h1>
            <p class="text-gray-600 mb-6">
                Aplikasi ini dibuat untuk memvisualisasikan <strong>peta batas wilayah Indonesia</strong> hingga level
                <strong>provinsi</strong> dan <strong>kabupaten/kota</strong> menggunakan teknologi <strong>Laravel</strong>,
                <strong>Leaflet.js</strong>, dan <strong>OpenStreetMap</strong>.
            </p>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-pink-50 border border-pink-100 rounded-2xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Fitur Utama</h2>
                    <ul class="text-gray-600 text-sm space-y-1 list-disc list-inside">
                        <li>Visualisasi batas wilayah provinsi dan kabupaten/kota.</li>
                        <li>Warna unik untuk setiap provinsi agar mudah dibedakan.</li>
                        <li>Hover dan klik untuk melihat nama wilayah dan zoom otomatis.</li>
                        <li>Info card untuk menampilkan ringkasan wilayah dan zoom level.</li>
                        <li>Pencarian lokasi sederhana dengan Nominatim (OpenStreetMap).</li>
                    </ul>
                </div>
                <div class="bg-purple-50 border border-purple-100 rounded-2xl p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Teknologi</h2>
                    <ul class="text-gray-600 text-sm space-y-1 list-disc list-inside">
                        <li>Laravel 12 (PHP Framework).</li>
                        <li>Leaflet.js untuk peta interaktif.</li>
                        <li>OpenStreetMap sebagai penyedia tile peta.</li>
                        <li>Tailwind CSS untuk tampilan antarmuka.</li>
                        <li>GeoJSON sebagai format data batas wilayah.</li>
                    </ul>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Tujuan</h2>
                <p class="text-gray-600 text-sm">
                    Tujuan utama aplikasi ini adalah membantu proses <strong>belajar geospasial</strong>, perencanaan wilayah,
                    dan visualisasi data geografis Indonesia dengan cara yang sederhana, ringan, dan mudah diakses melalui web.
                </p>
            </div>

            <div class="text-sm text-gray-500">
                <p>Dibuat oleh <strong class="text-purple-600">Edi Suherlan</strong>.</p>
                <p class="mt-1">
                    Source code tersedia di GitHub:
                    <a href="https://github.com/edisuherlan/peta-batas-wilayah-indonesia-laravel" target="_blank" class="text-purple-600 hover:underline">
                        github.com/edisuherlan/peta-batas-wilayah-indonesia-laravel
                    </a>
                </p>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-purple-600 hover:text-pink-500">
                ← Kembali ke Peta
            </a>
        </div>
    </main>
</body>
</html>


