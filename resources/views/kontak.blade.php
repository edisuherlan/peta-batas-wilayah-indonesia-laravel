<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontak - Peta Batas Wilayah Indonesia</title>
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
                <a href="{{ route('tentang') }}" class="text-gray-600 hover:text-purple-500 transition-colors">Tentang</a>
                <a href="{{ route('kontak') }}" class="text-purple-600 font-semibold">Kontak</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-10 max-w-3xl">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-pink-100 p-8 md:p-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Kontak</h1>
            <p class="text-gray-600 mb-6">
                Jika kamu punya saran, masukan, atau menemukan bug pada aplikasi ini, kamu bisa menghubungi saya melalui informasi di bawah ini.
            </p>

            <div class="space-y-4 mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-400 to-purple-400 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m0 0l-3-3m3 3l3-3" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nama</p>
                        <p class="font-semibold text-gray-800">Edi Suherlan</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-indigo-400 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m0 0l4-4m-4 4l4 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <a href="mailto:edisuherlan@gmail.com" class="font-semibold text-purple-600 hover:underline">
                            edisuherlan@gmail.com
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-sky-400 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.657-1.343 3-3 3S6 12.657 6 11s1.343-3 3-3 3 1.343 3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 4.418 3.582 8 8 8" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Website</p>
                        <a href="https://audhighasu.com" target="_blank" class="font-semibold text-purple-600 hover:underline">
                            audhighasu.com
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.586-2.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">GitHub</p>
                        <a href="https://github.com/edisuherlan" target="_blank" class="font-semibold text-purple-600 hover:underline">
                            @edisuherlan
                        </a>
                    </div>
                </div>
            </div>

            <p class="text-xs text-gray-500">
                Catatan: Halaman ini hanya menampilkan informasi kontak statis.
                Jika ingin menambahkan form kontak yang benar-benar mengirim email, bisa ditambahkan nanti dengan fitur Laravel Mail.
            </p>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-purple-600 hover:text-pink-500">
                ← Kembali ke Peta
            </a>
        </div>
    </main>
</body>
</html>


