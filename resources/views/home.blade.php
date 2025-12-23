{{--
    |--------------------------------------------------------------------------
    | Map Application - Peta Batas Wilayah Indonesia
    |--------------------------------------------------------------------------
    |
    | Created by: Edi Suherlan
    | Email: edisuherlan@gmail.com
    | Website: https://audhighasu.com
    |
    | Dibuat menggunakan Laravel 12, Leaflet.js, dan OpenStreetMap
    |
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map Application - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        #map {
            height: 100%;
            width: 100%;
            border-radius: 1rem;
            z-index: 1;
        }
        .leaflet-container {
            background: #f5f3f0 !important;
        }
        .kabupaten-label {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            color: #4b5563 !important;
            pointer-events: none !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(6px);
            line-height: 1.4 !important;
        }
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2) !important;
            padding: 8px !important;
        }
        .watermark {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 11px;
            color: #6b7280;
            z-index: 1000;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .watermark a {
            color: #9333ea;
            text-decoration: none;
            font-weight: 500;
        }
        .watermark a:hover {
            color: #a855f7;
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-pink-100">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-300 to-purple-400 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-pink-400 via-purple-500 to-indigo-500 bg-clip-text text-transparent">
                        Peta Batas Wilayah Indonesia
                    </h1>
                </div>
                <nav class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-purple-600 hover:text-pink-500 transition-colors font-medium">Beranda</a>
                    <a href="#" class="text-gray-600 hover:text-purple-500 transition-colors">Tentang</a>
                    <a href="#" class="text-gray-600 hover:text-purple-500 transition-colors">Kontak</a>
                </nav>
                <button class="md:hidden p-2 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Search Bar -->
        <div class="mb-6">
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Cari lokasi..." 
                        class="w-full px-6 py-4 pl-14 bg-white/90 backdrop-blur-sm border-2 border-pink-200 rounded-2xl focus:border-pink-400 focus:ring-4 focus:ring-pink-100 outline-none transition-all shadow-lg text-gray-700 placeholder-gray-400"
                    >
                    <div class="absolute left-5 top-1/2 transform -translate-y-1/2">
                        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="bg-white/60 backdrop-blur-sm rounded-3xl shadow-2xl p-4 md:p-6 border border-pink-100">
            <div class="h-[600px] md:h-[700px] rounded-2xl overflow-hidden relative">
                <div id="map"></div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-gradient-to-br from-pink-100 to-pink-200/50 rounded-2xl p-6 shadow-lg border border-pink-200/50 hover:shadow-xl transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-400 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Wilayah</h3>
                        <p class="text-sm text-gray-600" id="locationInfo">Banten, Jakarta, Jabar, Jateng & Jatim<br><span class="text-xs">Loading...</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-100 to-purple-200/50 rounded-2xl p-6 shadow-lg border border-purple-200/50 hover:shadow-xl transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-400 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Zoom Level</h3>
                        <p class="text-sm text-gray-600" id="zoomLevel">13</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-100 to-indigo-200/50 rounded-2xl p-6 shadow-lg border border-indigo-200/50 hover:shadow-xl transition-shadow">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-400 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Koordinat</h3>
                        <p class="text-sm text-gray-600 font-mono" id="coordinates">-6.3, 106.1</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Watermark -->
    <div class="watermark">
        Created by <a href="mailto:edisuherlan@gmail.com">Edi Suherlan</a> | 
        <a href="https://audhighasu.com" target="_blank">audhighasu.com</a>
    </div>

    <!-- Footer -->
    <footer class="mt-12 bg-white/60 backdrop-blur-sm border-t border-pink-100">
        <div class="container mx-auto px-4 py-6">
            <div class="text-center text-gray-600">
                <p>&copy; {{ date('Y') }} Map Application. Dibuat dengan ❤️ menggunakan Laravel & OpenStreetMap</p>
                <p class="text-xs text-gray-500 mt-2">
                    Created by <a href="mailto:edisuherlan@gmail.com" class="text-purple-600 hover:text-pink-500 transition-colors">Edi Suherlan</a> | 
                    <a href="https://audhighasu.com" target="_blank" class="text-purple-600 hover:text-pink-500 transition-colors">audhighasu.com</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

    <script>
        // Initialize map centered on Banten
        // Initialize map focused on Indonesia
        const map = L.map('map').setView([-2.0, 118.0], 5);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Style for boundary polygon with soft pastel colors
        const boundaryStyle = {
            fillColor: '#f9a8d4',
            fillOpacity: 0.3,
            color: '#ec4899',
            weight: 3,
            opacity: 0.8,
            dashArray: '10, 5'
        };

        const hoverStyle = {
            fillColor: '#f472b6',
            fillOpacity: 0.4,
            color: '#db2777',
            weight: 4,
            opacity: 1
        };

        // Array of consistent pink/purple pastel colors for Banten kabupaten/kota
        const pastelColors = [
            { fill: '#fce7f3', stroke: '#f9a8d4' }, // Light Pink
            { fill: '#fbcfe8', stroke: '#f472b6' }, // Pink
            { fill: '#f9a8d4', stroke: '#ec4899' }, // Medium Pink
            { fill: '#f5d0fe', stroke: '#e879f9' }, // Light Purple
            { fill: '#e9d5ff', stroke: '#d8b4fe' }, // Purple
            { fill: '#ddd6fe', stroke: '#c4b5fd' }, // Medium Purple
            { fill: '#ede9fe', stroke: '#c7d2fe' }, // Lavender
            { fill: '#f3e8ff', stroke: '#e9d5ff' }  // Light Lavender
        ];

        // Load and display Banten boundary
        let bantenLayer;
        fetch('/geojson/banten.geojson')
            .then(response => response.json())
            .then(data => {
                // Add GeoJSON layer with custom style
                bantenLayer = L.geoJSON(data, {
                    style: boundaryStyle,
                    onEachFeature: function(feature, layer) {
                        // Add hover effects
                        layer.on({
                            mouseover: function(e) {
                                const layer = e.target;
                                layer.setStyle(hoverStyle);
                                layer.bindPopup(`<div class="text-center"><strong>Provinsi Banten</strong><br>Batas Wilayah</div>`).openPopup();
                            },
                            mouseout: function(e) {
                                bantenLayer.resetStyle(e.target);
                            },
                            click: function(e) {
                                map.fitBounds(e.target.getBounds());
                            }
                        });
                    }
                }).addTo(map);

                // Load and display all kabupaten/kota boundaries
                const kabupatenKota = [
                    'Cilegon',
                    'Kota Serang',
                    'Kota Tangerang',
                    'Lebak',
                    'Pandeglang',
                    'Serang',
                    'Tangerang',
                    'Tangerang Selatan'
                ];

                let kabupatenLayers = [];
                let loadedCount = 0;
                let jakartaLayer = null;
                let jakartaKotaLayers = [];
                let jakartaKotaLoadedCount = 0;
                let jawaBaratLayer = null;
                let jawaBaratKotaLayers = [];
                let jawaBaratKotaLoadedCount = 0;
                let jawaTengahLayer = null;
                let jawaTengahKotaLayers = [];
                let jawaTengahKotaLoadedCount = 0;
                let jawaTimurLayer = null;
                let jawaTimurKotaLayers = [];
                let jawaTimurKotaLoadedCount = 0;
                let yogyakartaLayer = null;
                let yogyakartaKotaLayers = [];
                let yogyakartaKotaLoadedCount = 0;
                let baliLayer = null;
                let baliKotaLayers = [];
                let baliKotaLoadedCount = 0;
                let ntbLayer = null;
                let ntbKotaLayers = [];
                let ntbKotaLoadedCount = 0;
                let nttLayer = null;
                let nttKotaLayers = [];
                let nttKotaLoadedCount = 0;
                let papuaLayer = null;
                let papuaKotaLayers = [];
                let papuaKotaLoadedCount = 0;
                let papuaBaratLayer = null;
                let papuaBaratKotaLayers = [];
                let papuaBaratKotaLoadedCount = 0;
                let acehLayer = null;
                let acehKotaLayers = [];
                let acehKotaLoadedCount = 0;
                let bangkaBelitungLayer = null;
                let bangkaBelitungKotaLayers = [];
                let bangkaBelitungKotaLoadedCount = 0;
                let bengkuluLayer = null;
                let bengkuluKotaLayers = [];
                let bengkuluKotaLoadedCount = 0;
                let gorontaloLayer = null;
                let gorontaloKotaLayers = [];
                let gorontaloKotaLoadedCount = 0;
                let jambiLayer = null;
                let jambiKotaLayers = [];
                let jambiKotaLoadedCount = 0;
                let kalimantanBaratLayer = null;
                let kalimantanBaratKotaLayers = [];
                let kalimantanBaratKotaLoadedCount = 0;
                let kalimantanSelatanLayer = null;
                let kalimantanSelatanKotaLayers = [];
                let kalimantanSelatanKotaLoadedCount = 0;
                let kalimantanTengahLayer = null;
                let kalimantanTengahKotaLayers = [];
                let kalimantanTengahKotaLoadedCount = 0;
                let kalimantanTimurLayer = null;
                let kalimantanTimurKotaLayers = [];
                let kalimantanTimurKotaLoadedCount = 0;
                let kalimantanUtaraLayer = null;
                let kalimantanUtaraKotaLayers = [];
                let kalimantanUtaraKotaLoadedCount = 0;
                let kepulauanRiauLayer = null;
                let kepulauanRiauKotaLayers = [];
                let kepulauanRiauKotaLoadedCount = 0;
                let lampungLayer = null;
                let lampungKotaLayers = [];
                let lampungKotaLoadedCount = 0;
                let malukuLayer = null;
                let malukuKotaLayers = [];
                let malukuKotaLoadedCount = 0;
                let malukuUtaraLayer = null;
                let malukuUtaraKotaLayers = [];
                let malukuUtaraKotaLoadedCount = 0;
                
                // Function to fit bounds when all layers are loaded
                function fitAllBounds() {
                    const totalExpected = kabupatenKota.length + 6 + 27 + 36 + 38 + 5 + 9 + 10 + 21 + 29 + 11 + 23 + 7 + 10 + 7 + 11 + 14 + 13 + 14 + 9 + 5 + 7 + 14 + 11 + 9; // Banten + Jakarta + Jawa Barat + Jawa Tengah + Jawa Timur + Yogyakarta + Bali + NTB + NTT + Papua + Papua Barat + Aceh + Bangka Belitung + Bengkulu + Gorontalo + Jambi + Kalimantan Barat + Kalimantan Selatan + Kalimantan Tengah + Kalimantan Timur + Kalimantan Utara + Kepulauan Riau + Lampung + Maluku + Maluku Utara
                    const totalLoaded = loadedCount + jakartaKotaLoadedCount + jawaBaratKotaLoadedCount + jawaTengahKotaLoadedCount + jawaTimurKotaLoadedCount + yogyakartaKotaLoadedCount + baliKotaLoadedCount + ntbKotaLoadedCount + nttKotaLoadedCount + papuaKotaLoadedCount + papuaBaratKotaLoadedCount + acehKotaLoadedCount + bangkaBelitungKotaLoadedCount + bengkuluKotaLoadedCount + gorontaloKotaLoadedCount + jambiKotaLoadedCount + kalimantanBaratKotaLoadedCount + kalimantanSelatanKotaLoadedCount + kalimantanTengahKotaLoadedCount + kalimantanTimurKotaLoadedCount + kalimantanUtaraKotaLoadedCount + kepulauanRiauKotaLoadedCount + lampungKotaLoadedCount + malukuKotaLoadedCount + malukuUtaraKotaLoadedCount;
                    const allLayersReady = bantenLayer && jakartaLayer && jawaBaratLayer && jawaTengahLayer && jawaTimurLayer && yogyakartaLayer && baliLayer && ntbLayer && nttLayer && papuaLayer && papuaBaratLayer && acehLayer && bangkaBelitungLayer && bengkuluLayer && gorontaloLayer && jambiLayer && kalimantanBaratLayer && kalimantanSelatanLayer && kalimantanTengahLayer && kalimantanTimurLayer && kalimantanUtaraLayer && kepulauanRiauLayer && lampungLayer && malukuLayer && malukuUtaraLayer;
                    
                    if (totalLoaded === totalExpected && allLayersReady) {
                        const allLayers = new L.featureGroup([
                            bantenLayer, 
                            jakartaLayer, 
                            jawaBaratLayer,
                            jawaTengahLayer,
                            jawaTimurLayer,
                            yogyakartaLayer,
                            baliLayer,
                            ntbLayer,
                            nttLayer,
                            papuaLayer,
                            papuaBaratLayer,
                            acehLayer,
                            bangkaBelitungLayer,
                            bengkuluLayer,
                            gorontaloLayer,
                            jambiLayer,
                            kalimantanBaratLayer,
                            kalimantanSelatanLayer,
                            kalimantanTengahLayer,
                            kalimantanTimurLayer,
                            kalimantanUtaraLayer,
                            kepulauanRiauLayer,
                            lampungLayer,
                            malukuLayer,
                            malukuUtaraLayer,
                            ...kabupatenLayers, 
                            ...jakartaKotaLayers,
                            ...jawaBaratKotaLayers,
                            ...jawaTengahKotaLayers,
                            ...jawaTimurKotaLayers,
                            ...yogyakartaKotaLayers,
                            ...baliKotaLayers,
                            ...ntbKotaLayers,
                            ...nttKotaLayers,
                            ...papuaKotaLayers,
                            ...papuaBaratKotaLayers,
                            ...acehKotaLayers,
                            ...bangkaBelitungKotaLayers,
                            ...bengkuluKotaLayers,
                            ...gorontaloKotaLayers,
                            ...jambiKotaLayers,
                            ...kalimantanBaratKotaLayers,
                            ...kalimantanSelatanKotaLayers,
                            ...kalimantanTengahKotaLayers,
                            ...kalimantanTimurKotaLayers,
                            ...kalimantanUtaraKotaLayers,
                            ...kepulauanRiauKotaLayers,
                            ...lampungKotaLayers,
                            ...malukuKotaLayers,
                            ...malukuUtaraKotaLayers
                        ]);
                        map.fitBounds(allLayers.getBounds(), { padding: [50, 50] });
                        const totalKota = kabupatenLayers.length + jakartaKotaLayers.length + jawaBaratKotaLayers.length + jawaTengahKotaLayers.length + jawaTimurKotaLayers.length + yogyakartaKotaLayers.length + baliKotaLayers.length + ntbKotaLayers.length + nttKotaLayers.length + papuaKotaLayers.length + papuaBaratKotaLayers.length + acehKotaLayers.length + bangkaBelitungKotaLayers.length + bengkuluKotaLayers.length + gorontaloKotaLayers.length + jambiKotaLayers.length + kalimantanBaratKotaLayers.length + kalimantanSelatanKotaLayers.length + kalimantanTengahKotaLayers.length + kalimantanTimurKotaLayers.length + kalimantanUtaraKotaLayers.length + kepulauanRiauKotaLayers.length + lampungKotaLayers.length + malukuKotaLayers.length + malukuUtaraKotaLayers.length;
                        document.getElementById('locationInfo').innerHTML = `Banten, Jakarta, Jabar, Jateng, Jatim, Yogyakarta, Bali, NTB, NTT, Papua, Papua Barat, Aceh, Babel, Bengkulu, Gorontalo, Jambi, Kalbar, Kalsel, Kalteng, Kaltim, Kalut, Kepri, Lampung, Maluku & Malut<br><span class=\"text-xs text-green-600 font-medium\">✓ ${totalKota} Kabupaten/Kota dimuat</span>`;
                    }
                }

                kabupatenKota.forEach((kabupaten, index) => {
                    const colorIndex = index % pastelColors.length;
                    const kabupatenStyle = {
                        fillColor: pastelColors[colorIndex].fill,
                        fillOpacity: 0.3,
                        color: pastelColors[colorIndex].stroke,
                        weight: 2.5,
                        opacity: 0.8,
                        dashArray: '8, 4'
                    };

                    const kabupatenHoverStyle = {
                        fillColor: pastelColors[colorIndex].fill,
                        fillOpacity: 0.5,
                        color: pastelColors[colorIndex].stroke,
                        weight: 4,
                        opacity: 1,
                        dashArray: '0'
                    };

                    // Convert nama kabupaten/kota to filename format (replace space with underscore)
                    const fileName = `banten_${kabupaten.replace(/ /g, '_')}.geojson`;
                    fetch(`/geojson/${fileName}`)
                        .then(response => response.json())
                        .then(data => {
                            const kabupatenLayer = L.geoJSON(data, {
                                style: kabupatenStyle,
                                onEachFeature: function(feature, layer) {
                                    // Get center for label
                                    const bounds = layer.getBounds();
                                    const center = bounds.getCenter();
                                    
                                    // Bind popup sekali di awal
                                    layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${kabupaten}</strong><br><span class="text-sm text-gray-600">Provinsi Banten</span></div>`, {
                                        className: 'custom-popup'
                                    });
                                    
                                    layer.on({
                                        mouseover: function(e) {
                                            const layer = e.target;
                                            layer.setStyle(kabupatenHoverStyle);
                                            layer.openPopup();
                                        },
                                        mouseout: function(e) {
                                            kabupatenLayer.resetStyle(e.target);
                                            layer.closePopup();
                                        },
                                        click: function(e) {
                                            map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                        }
                                    });
                                }
                            }).addTo(map);

                            kabupatenLayers.push(kabupatenLayer);
                            loadedCount++;

                            // Check if all layers are loaded
                            fitAllBounds();
                        })
                        .catch(error => {
                            console.error(`Error loading ${kabupaten}:`, error);
                            loadedCount++;
                            // Still check if all are done even if some failed
                            fitAllBounds();
                        });
                });

                // Load and display Jakarta boundary
                fetch('/geojson/jakarta.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Jakarta style dengan warna berbeda (biru/cyan)
                        const jakartaBoundaryStyle = {
                            fillColor: '#60a5fa',
                            fillOpacity: 0.25,
                            color: '#3b82f6',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const jakartaHoverStyle = {
                            fillColor: '#60a5fa',
                            fillOpacity: 0.4,
                            color: '#2563eb',
                            weight: 4,
                            opacity: 1
                        };

                        jakartaLayer = L.geoJSON(data, {
                            style: jakartaBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(jakartaHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>DKI Jakarta</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        jakartaLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Jakarta kota boundaries
                        const jakartaKota = [
                            'Jakarta Barat',
                            'Jakarta Pusat',
                            'Jakarta Selatan',
                            'Jakarta Timur',
                            'Jakarta Utara',
                            'Kepulauan Seribu'
                        ];

                        // Warna berbeda untuk Jakarta (cyan/sky blue tones)
                        const jakartaColors = [
                            { fill: '#67e8f9', stroke: '#06b6d4' }, // Cyan
                            { fill: '#7dd3fc', stroke: '#0ea5e9' }, // Sky
                            { fill: '#93c5fd', stroke: '#3b82f6' }, // Blue
                            { fill: '#a5b4fc', stroke: '#6366f1' }, // Indigo
                            { fill: '#c4b5fd', stroke: '#8b5cf6' }, // Violet
                            { fill: '#ddd6fe', stroke: '#a78bfa' }  // Purple
                        ];

                        jakartaKota.forEach((kota, index) => {
                            const colorIndex = index % jakartaColors.length;
                            const kotaStyle = {
                                fillColor: jakartaColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: jakartaColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: jakartaColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: jakartaColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `jakarta_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => response.json())
                                .then(data => {
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${kota}</strong><br><span class="text-sm text-gray-600">DKI Jakarta</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 12 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    jakartaKotaLayers.push(kotaLayer);
                                    jakartaKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota}:`, error);
                                    jakartaKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Jakarta GeoJSON:', error);
                    });

                // Load and display Jawa Barat boundary
                fetch('/geojson/jawa_barat.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Jawa Barat style dengan warna hijau/emerald
                        const jawaBaratBoundaryStyle = {
                            fillColor: '#34d399',
                            fillOpacity: 0.25,
                            color: '#10b981',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const jawaBaratHoverStyle = {
                            fillColor: '#34d399',
                            fillOpacity: 0.4,
                            color: '#059669',
                            weight: 4,
                            opacity: 1
                        };

                        jawaBaratLayer = L.geoJSON(data, {
                            style: jawaBaratBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(jawaBaratHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Jawa Barat</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        jawaBaratLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Jawa Barat kota/kabupaten boundaries
                        const jawaBaratKota = [
                            'Bandung', 'Bandung Barat', 'Banjar', 'Bekasi', 'Bogor', 'Ciamis',
                            'Cianjur', 'Cimahi', 'Cirebon', 'Depok', 'Garut', 'Indramayu',
                            'Karawang', 'Kota Bandung', 'Kota Bekasi', 'Kota Bogor', 'Kota Cirebon',
                            'Kota Sukabumi', 'Kota Tasikmalaya', 'Kuningan', 'Majalengka',
                            'Purwakarta', 'Subang', 'Sukabumi', 'Sumedang', 'Tasikmalaya', 'Waduk Cirata'
                        ];

                        // Warna hijau/emerald untuk Jawa Barat
                        const jawaBaratColors = [
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#d1fae5', stroke: '#059669' }, // Emerald
                            { fill: '#ecfdf5', stroke: '#047857' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#86efac', stroke: '#4ade80' }, // Green
                            { fill: '#6ee7b7', stroke: '#34d399' }, // Emerald
                            { fill: '#a7f3d0', stroke: '#10b981' }, // Green
                            { fill: '#5eead4', stroke: '#14b8a6' }, // Teal
                            { fill: '#86efac', stroke: '#4ade80' }  // Green
                        ];

                        jawaBaratKota.forEach((kota, index) => {
                            const colorIndex = index % jawaBaratColors.length;
                            const kotaStyle = {
                                fillColor: jawaBaratColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: jawaBaratColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: jawaBaratColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: jawaBaratColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `jawa_barat_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => response.json())
                                .then(data => {
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${kota}</strong><br><span class="text-sm text-gray-600">Jawa Barat</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    jawaBaratKotaLayers.push(kotaLayer);
                                    jawaBaratKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota}:`, error);
                                    jawaBaratKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Jawa Barat GeoJSON:', error);
                    });

                // Load and display Jawa Tengah boundary
                fetch('/geojson/jawa_tengah.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Jawa Tengah style dengan warna kuning/orange
                        const jawaTengahBoundaryStyle = {
                            fillColor: '#fbbf24',
                            fillOpacity: 0.25,
                            color: '#f59e0b',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const jawaTengahHoverStyle = {
                            fillColor: '#fbbf24',
                            fillOpacity: 0.4,
                            color: '#d97706',
                            weight: 4,
                            opacity: 1
                        };

                        jawaTengahLayer = L.geoJSON(data, {
                            style: jawaTengahBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(jawaTengahHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Jawa Tengah</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        jawaTengahLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Jawa Tengah kota/kabupaten boundaries
                        const jawaTengahKota = [
                            'Banjarnegara', 'Banyumas', 'Batang', 'Blora', 'Boyolali', 'Brebes',
                            'Cilacap', 'Demak', 'Grobogan', 'Jepara', 'Karanganyar', 'Kebumen',
                            'Kendal', 'Klaten', 'Kota Magelang', 'Kota Pekalongan', 'Kota Semarang',
                            'Kota Tegal', 'Kudus', 'Magelang', 'Pati', 'Pekalongan', 'Pemalang',
                            'Purbalingga', 'Purworejo', 'Rembang', 'Salatiga', 'Semarang', 'Sragen',
                            'Sukoharjo', 'Surakarta', 'Tegal', 'Temanggung', 'Waduk Kedungombo',
                            'Wonogiri', 'Wonosobo'
                        ];

                        // Warna kuning/orange untuk Jawa Tengah
                        const jawaTengahColors = [
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }, // Rose
                            { fill: '#fef3c7', stroke: '#fcd34d' }, // Amber
                            { fill: '#fde68a', stroke: '#fbbf24' }, // Yellow
                            { fill: '#fed7aa', stroke: '#fb923c' }, // Orange
                            { fill: '#fecaca', stroke: '#f87171' }  // Rose
                        ];

                        jawaTengahKota.forEach((kota, index) => {
                            const colorIndex = index % jawaTengahColors.length;
                            const kotaStyle = {
                                fillColor: jawaTengahColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: jawaTengahColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: jawaTengahColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: jawaTengahColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `jawa_tengah_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => response.json())
                                .then(data => {
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${kota}</strong><br><span class="text-sm text-gray-600">Jawa Tengah</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    jawaTengahKotaLayers.push(kotaLayer);
                                    jawaTengahKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota}:`, error);
                                    jawaTengahKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Jawa Tengah GeoJSON:', error);
                    });

                // Load and display Jawa Timur boundary
                fetch('/geojson/jawa_timur.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Jawa Timur style dengan warna merah/coral
                        const jawaTimurBoundaryStyle = {
                            fillColor: '#fb7185',
                            fillOpacity: 0.25,
                            color: '#f43f5e',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const jawaTimurHoverStyle = {
                            fillColor: '#fb7185',
                            fillOpacity: 0.4,
                            color: '#e11d48',
                            weight: 4,
                            opacity: 1
                        };

                        jawaTimurLayer = L.geoJSON(data, {
                            style: jawaTimurBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(jawaTimurHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Jawa Timur</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        jawaTimurLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Jawa Timur kota/kabupaten boundaries
                        const jawaTimurKota = [
                            'Bangkalan', 'Banyuwangi', 'Batu', 'Blitar', 'Bojonegoro', 'Bondowoso',
                            'Gresik', 'Jember', 'Jombang', 'Kediri', 'Kota Blitar', 'Kota Kediri',
                            'Kota Madiun', 'Kota Malang', 'Kota Mojokerto', 'Kota Pasuruan', 'Kota Probolinggo',
                            'Lamongan', 'Lumajang', 'Madiun', 'Magetan', 'Malang', 'Mojokerto',
                            'Nganjuk', 'Ngawi', 'Pacitan', 'Pamekasan', 'Pasuruan', 'Ponorogo',
                            'Probolinggo', 'Sampang', 'Sidoarjo', 'Situbondo', 'Sumenep', 'Surabaya',
                            'Trenggalek', 'Tuban', 'Tulungagung'
                        ];

                        // Warna merah/coral/rose untuk Jawa Timur
                        const jawaTimurColors = [
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }, // Rose
                            { fill: '#fda4af', stroke: '#fb7185' }, // Rose
                            { fill: '#fecdd3', stroke: '#f43f5e' }, // Pink
                            { fill: '#ffe4e6', stroke: '#e11d48' }  // Rose
                        ];

                        jawaTimurKota.forEach((kota, index) => {
                            const colorIndex = index % jawaTimurColors.length;
                            const kotaStyle = {
                                fillColor: jawaTimurColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: jawaTimurColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: jawaTimurColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: jawaTimurColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `jawa_timur_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        jawaTimurKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Jawa Timur</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    jawaTimurKotaLayers.push(kotaLayer);
                                    jawaTimurKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    jawaTimurKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Jawa Timur GeoJSON:', error);
                    });

                // Load and display Yogyakarta boundary
                fetch('/geojson/yogyakarta.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Yogyakarta style dengan warna indigo/violet
                        const yogyakartaBoundaryStyle = {
                            fillColor: '#818cf8',
                            fillOpacity: 0.25,
                            color: '#6366f1',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const yogyakartaHoverStyle = {
                            fillColor: '#818cf8',
                            fillOpacity: 0.4,
                            color: '#4f46e5',
                            weight: 4,
                            opacity: 1
                        };

                        yogyakartaLayer = L.geoJSON(data, {
                            style: yogyakartaBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(yogyakartaHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Yogyakarta</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        yogyakartaLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Yogyakarta kota/kabupaten boundaries
                        const yogyakartaKota = [
                            'Bantul', 'Gunung Kidul', 'Kota Yogyakarta', 'Kulon Progo', 'Sleman'
                        ];

                        // Warna indigo/violet untuk Yogyakarta (konsisten)
                        const yogyakartaColors = [
                            { fill: '#e0e7ff', stroke: '#818cf8' }, // Light Indigo
                            { fill: '#c7d2fe', stroke: '#6366f1' }, // Medium Indigo
                            { fill: '#a5b4fc', stroke: '#4f46e5' }, // Dark Indigo
                            { fill: '#e0e7ff', stroke: '#818cf8' }, // Light Indigo
                            { fill: '#c7d2fe', stroke: '#6366f1' }  // Medium Indigo
                        ];

                        yogyakartaKota.forEach((kota, index) => {
                            const colorIndex = index % yogyakartaColors.length;
                            const kotaStyle = {
                                fillColor: yogyakartaColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: yogyakartaColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: yogyakartaColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: yogyakartaColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `yogyakarta_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        yogyakartaKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Yogyakarta</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    yogyakartaKotaLayers.push(kotaLayer);
                                    yogyakartaKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    yogyakartaKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Yogyakarta GeoJSON:', error);
                    });

                // Load and display Bali boundary
                fetch('/geojson/bali.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Bali style dengan warna teal/turquoise
                        const baliBoundaryStyle = {
                            fillColor: '#14b8a6',
                            fillOpacity: 0.25,
                            color: '#0d9488',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const baliHoverStyle = {
                            fillColor: '#14b8a6',
                            fillOpacity: 0.4,
                            color: '#0f766e',
                            weight: 4,
                            opacity: 1
                        };

                        baliLayer = L.geoJSON(data, {
                            style: baliBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(baliHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Bali</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        baliLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Bali kota/kabupaten boundaries
                        const baliKota = [
                            'Badung', 'Bangli', 'Buleleng', 'Denpasar', 'Gianyar', 'Jembrana', 'Karangasem', 'Klungkung', 'Tabanan'
                        ];

                        // Warna teal/turquoise untuk Bali (konsisten)
                        const baliColors = [
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }  // Dark Teal
                        ];

                        baliKota.forEach((kota, index) => {
                            const colorIndex = index % baliColors.length;
                            const kotaStyle = {
                                fillColor: baliColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: baliColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: baliColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: baliColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `bali_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        baliKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Bali</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    baliKotaLayers.push(kotaLayer);
                                    baliKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    baliKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Bali GeoJSON:', error);
                    });

                // Load and display Nusa Tenggara Barat boundary
                fetch('/geojson/nusa_tenggara_barat.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // NTB style dengan warna amber/orange
                        const ntbBoundaryStyle = {
                            fillColor: '#f59e0b',
                            fillOpacity: 0.25,
                            color: '#d97706',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const ntbHoverStyle = {
                            fillColor: '#f59e0b',
                            fillOpacity: 0.4,
                            color: '#b45309',
                            weight: 4,
                            opacity: 1
                        };

                        ntbLayer = L.geoJSON(data, {
                            style: ntbBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(ntbHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Nusa Tenggara Barat</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        ntbLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all NTB kota/kabupaten boundaries
                        const ntbKota = [
                            'Bima', 'Dompu', 'Kota Bima', 'Lombok Barat', 'Lombok Tengah', 'Lombok Timur', 'Lombok Utara', 'Mataram', 'Sumbawa', 'Sumbawa Barat'
                        ];

                        // Warna amber/orange untuk NTB (konsisten)
                        const ntbColors = [
                            { fill: '#fef3c7', stroke: '#f59e0b' }, // Light Amber
                            { fill: '#fde68a', stroke: '#d97706' }, // Medium Amber
                            { fill: '#fcd34d', stroke: '#b45309' }, // Dark Amber
                            { fill: '#fef3c7', stroke: '#f59e0b' }, // Light Amber
                            { fill: '#fde68a', stroke: '#d97706' }, // Medium Amber
                            { fill: '#fcd34d', stroke: '#b45309' }, // Dark Amber
                            { fill: '#fef3c7', stroke: '#f59e0b' }, // Light Amber
                            { fill: '#fde68a', stroke: '#d97706' }, // Medium Amber
                            { fill: '#fcd34d', stroke: '#b45309' }, // Dark Amber
                            { fill: '#fef3c7', stroke: '#f59e0b' }  // Light Amber
                        ];

                        ntbKota.forEach((kota, index) => {
                            const colorIndex = index % ntbColors.length;
                            const kotaStyle = {
                                fillColor: ntbColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: ntbColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: ntbColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: ntbColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `ntb_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        ntbKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Nusa Tenggara Barat</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    ntbKotaLayers.push(kotaLayer);
                                    ntbKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    ntbKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Nusa Tenggara Barat GeoJSON:', error);
                    });

                // Load and display Nusa Tenggara Timur boundary
                fetch('/geojson/nusa_tenggara_timur.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // NTT style dengan warna lime/hijau muda
                        const nttBoundaryStyle = {
                            fillColor: '#84cc16',
                            fillOpacity: 0.25,
                            color: '#65a30d',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const nttHoverStyle = {
                            fillColor: '#84cc16',
                            fillOpacity: 0.4,
                            color: '#4d7c0f',
                            weight: 4,
                            opacity: 1
                        };

                        nttLayer = L.geoJSON(data, {
                            style: nttBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(nttHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Nusa Tenggara Timur</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        nttLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all NTT kota/kabupaten boundaries
                        const nttKota = [
                            'Alor', 'Belu', 'Ende', 'Flores Timur', 'Kota Kupang', 'Kupang', 'Lembata', 'Manggarai', 'Manggarai Barat', 'Manggarai Timur',
                            'Nagekeo', 'Ngada', 'Rote Ndao', 'Sabu Raijua', 'Sikka', 'Sumba Barat', 'Sumba Barat Daya', 'Sumba Tengah', 'Sumba Timur', 'Timor Tengah Selatan', 'Timor Tengah Utara'
                        ];

                        // Warna lime/hijau muda untuk NTT (konsisten)
                        const nttColors = [
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }, // Dark Lime
                            { fill: '#ecfccb', stroke: '#84cc16' }, // Light Lime
                            { fill: '#d9f99d', stroke: '#65a30d' }, // Medium Lime
                            { fill: '#bef264', stroke: '#4d7c0f' }  // Dark Lime
                        ];

                        nttKota.forEach((kota, index) => {
                            const colorIndex = index % nttColors.length;
                            const kotaStyle = {
                                fillColor: nttColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: nttColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: nttColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: nttColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `ntt_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        nttKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Nusa Tenggara Timur</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    nttKotaLayers.push(kotaLayer);
                                    nttKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    nttKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Nusa Tenggara Timur GeoJSON:', error);
                    });

                // Load and display Papua boundary
                fetch('/geojson/papua.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Papua style dengan warna slate/gray
                        const papuaBoundaryStyle = {
                            fillColor: '#64748b',
                            fillOpacity: 0.25,
                            color: '#475569',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const papuaHoverStyle = {
                            fillColor: '#64748b',
                            fillOpacity: 0.4,
                            color: '#334155',
                            weight: 4,
                            opacity: 1
                        };

                        papuaLayer = L.geoJSON(data, {
                            style: papuaBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(papuaHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Papua</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        papuaLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Papua kota/kabupaten boundaries
                        const papuaKota = [
                            'Asmat', 'Biak Numfor', 'Boven Digoel', 'Deiyai', 'Dogiyai', 'Intan Jaya', 'Jayapura', 'Jayawijaya', 'Keerom', 'Kepulauan Yapen',
                            'Kota Jayapura', 'Lanny Jaya', 'Mamberamo Raya', 'Mamberamo Tengah', 'Mappi', 'Merauke', 'Mimika', 'Nabire', 'Nduga', 'Paniai',
                            'Pegunungan Bintang', 'Puncak', 'Puncak Jaya', 'Sarmi', 'Supiori', 'Tolikara', 'Waropen', 'Yahukimo', 'Yalimo'
                        ];

                        // Warna slate/gray untuk Papua (konsisten)
                        const papuaColors = [
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }, // Dark Slate
                            { fill: '#f1f5f9', stroke: '#64748b' }, // Light Slate
                            { fill: '#e2e8f0', stroke: '#475569' }, // Medium Slate
                            { fill: '#cbd5e1', stroke: '#334155' }  // Dark Slate
                        ];

                        papuaKota.forEach((kota, index) => {
                            const colorIndex = index % papuaColors.length;
                            const kotaStyle = {
                                fillColor: papuaColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: papuaColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: papuaColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: papuaColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `papua_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        papuaKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Papua</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    papuaKotaLayers.push(kotaLayer);
                                    papuaKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    papuaKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Papua GeoJSON:', error);
                    });

                // Load and display Papua Barat boundary
                fetch('/geojson/papua_barat.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Papua Barat style dengan warna cyan/sky blue
                        const papuaBaratBoundaryStyle = {
                            fillColor: '#06b6d4',
                            fillOpacity: 0.25,
                            color: '#0891b2',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const papuaBaratHoverStyle = {
                            fillColor: '#06b6d4',
                            fillOpacity: 0.4,
                            color: '#0e7490',
                            weight: 4,
                            opacity: 1
                        };

                        papuaBaratLayer = L.geoJSON(data, {
                            style: papuaBaratBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(papuaBaratHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Papua Barat</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        papuaBaratLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Papua Barat kota/kabupaten boundaries
                        const papuaBaratKota = [
                            'Fakfak', 'Kaimana', 'Kota Sorong', 'Manokwari', 'Maybrat', 'Raja Ampat', 'Sorong', 'Sorong Selatan', 'Tambrauw', 'Teluk Bintuni', 'Teluk Wondama'
                        ];

                        // Warna cyan/sky blue untuk Papua Barat (konsisten)
                        const papuaBaratColors = [
                            { fill: '#cffafe', stroke: '#06b6d4' }, // Light Cyan
                            { fill: '#a5f3fc', stroke: '#0891b2' }, // Medium Cyan
                            { fill: '#67e8f9', stroke: '#0e7490' }, // Dark Cyan
                            { fill: '#cffafe', stroke: '#06b6d4' }, // Light Cyan
                            { fill: '#a5f3fc', stroke: '#0891b2' }, // Medium Cyan
                            { fill: '#67e8f9', stroke: '#0e7490' }, // Dark Cyan
                            { fill: '#cffafe', stroke: '#06b6d4' }, // Light Cyan
                            { fill: '#a5f3fc', stroke: '#0891b2' }, // Medium Cyan
                            { fill: '#67e8f9', stroke: '#0e7490' }, // Dark Cyan
                            { fill: '#cffafe', stroke: '#06b6d4' }, // Light Cyan
                            { fill: '#a5f3fc', stroke: '#0891b2' }  // Medium Cyan
                        ];

                        papuaBaratKota.forEach((kota, index) => {
                            const colorIndex = index % papuaBaratColors.length;
                            const kotaStyle = {
                                fillColor: papuaBaratColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: papuaBaratColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: papuaBaratColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: papuaBaratColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `papua_barat_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        papuaBaratKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Papua Barat</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    papuaBaratKotaLayers.push(kotaLayer);
                                    papuaBaratKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    papuaBaratKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Papua Barat GeoJSON:', error);
                    });

                // Load and display Aceh boundary
                fetch('/geojson/aceh.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Aceh style dengan warna emerald/zinc
                        const acehBoundaryStyle = {
                            fillColor: '#10b981',
                            fillOpacity: 0.25,
                            color: '#059669',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const acehHoverStyle = {
                            fillColor: '#10b981',
                            fillOpacity: 0.4,
                            color: '#047857',
                            weight: 4,
                            opacity: 1
                        };

                        acehLayer = L.geoJSON(data, {
                            style: acehBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(acehHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Aceh</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        acehLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Aceh kota/kabupaten boundaries
                        const acehKota = [
                            'Aceh Barat', 'Aceh Barat Daya', 'Aceh Besar', 'Aceh Jaya', 'Aceh Selatan', 'Aceh Singkil', 'Aceh Tamiang', 'Aceh Tengah', 'Aceh Tenggara', 'Aceh Timur', 'Aceh Utara',
                            'Banda Aceh', 'Bener Meriah', 'Bireuen', 'Gayo Lues', 'Langsa', 'Lhokseumawe', 'Nagan Raya', 'Pidie', 'Pidie Jaya', 'Sabang', 'Simeulue', 'Subulussalam'
                        ];

                        // Warna emerald untuk Aceh (konsisten)
                        const acehColors = [
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }, // Medium Emerald
                            { fill: '#6ee7b7', stroke: '#047857' }, // Dark Emerald
                            { fill: '#d1fae5', stroke: '#10b981' }, // Light Emerald
                            { fill: '#a7f3d0', stroke: '#059669' }  // Medium Emerald
                        ];

                        acehKota.forEach((kota, index) => {
                            const colorIndex = index % acehColors.length;
                            const kotaStyle = {
                                fillColor: acehColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: acehColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: acehColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: acehColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `aceh_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        acehKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Aceh</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    acehKotaLayers.push(kotaLayer);
                                    acehKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    acehKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Aceh GeoJSON:', error);
                    });

                // Load and display Bangka Belitung boundary
                fetch('/geojson/bangka_belitung.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Bangka Belitung style dengan warna stone/neutral
                        const bangkaBelitungBoundaryStyle = {
                            fillColor: '#78716c',
                            fillOpacity: 0.25,
                            color: '#57534e',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const bangkaBelitungHoverStyle = {
                            fillColor: '#78716c',
                            fillOpacity: 0.4,
                            color: '#44403c',
                            weight: 4,
                            opacity: 1
                        };

                        bangkaBelitungLayer = L.geoJSON(data, {
                            style: bangkaBelitungBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(bangkaBelitungHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Bangka Belitung</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        bangkaBelitungLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Bangka Belitung kota/kabupaten boundaries
                        const bangkaBelitungKota = [
                            'Bangka', 'Bangka Barat', 'Bangka Selatan', 'Bangka Tengah', 'Belitung', 'Belitung Timur', 'Pangkalpinang'
                        ];

                        // Warna stone/neutral untuk Bangka Belitung (konsisten)
                        const bangkaBelitungColors = [
                            { fill: '#fafaf9', stroke: '#78716c' }, // Light Stone
                            { fill: '#f5f5f4', stroke: '#57534e' }, // Medium Stone
                            { fill: '#e7e5e4', stroke: '#44403c' }, // Dark Stone
                            { fill: '#fafaf9', stroke: '#78716c' }, // Light Stone
                            { fill: '#f5f5f4', stroke: '#57534e' }, // Medium Stone
                            { fill: '#e7e5e4', stroke: '#44403c' }, // Dark Stone
                            { fill: '#fafaf9', stroke: '#78716c' }  // Light Stone
                        ];

                        bangkaBelitungKota.forEach((kota, index) => {
                            const colorIndex = index % bangkaBelitungColors.length;
                            const kotaStyle = {
                                fillColor: bangkaBelitungColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: bangkaBelitungColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: bangkaBelitungColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: bangkaBelitungColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `bangka_belitung_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        bangkaBelitungKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Bangka Belitung</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    bangkaBelitungKotaLayers.push(kotaLayer);
                                    bangkaBelitungKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    bangkaBelitungKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Bangka Belitung GeoJSON:', error);
                    });

                // Load and display Bengkulu boundary
                fetch('/geojson/bengkulu.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Bengkulu style dengan warna zinc/neutral
                        const bengkuluBoundaryStyle = {
                            fillColor: '#71717a',
                            fillOpacity: 0.25,
                            color: '#52525b',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const bengkuluHoverStyle = {
                            fillColor: '#71717a',
                            fillOpacity: 0.4,
                            color: '#3f3f46',
                            weight: 4,
                            opacity: 1
                        };

                        bengkuluLayer = L.geoJSON(data, {
                            style: bengkuluBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(bengkuluHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Bengkulu</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        bengkuluLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Bengkulu kota/kabupaten boundaries
                        const bengkuluKota = [
                            'Bengkulu', 'Bengkulu Selatan', 'Bengkulu Tengah', 'Bengkulu Utara', 'Kaur', 'Kepahiang', 'Lebong', 'Mukomuko', 'Rejang Lebong', 'Seluma'
                        ];

                        // Warna zinc/neutral untuk Bengkulu (konsisten)
                        const bengkuluColors = [
                            { fill: '#fafafa', stroke: '#71717a' }, // Light Zinc
                            { fill: '#f4f4f5', stroke: '#52525b' }, // Medium Zinc
                            { fill: '#e4e4e7', stroke: '#3f3f46' }, // Dark Zinc
                            { fill: '#fafafa', stroke: '#71717a' }, // Light Zinc
                            { fill: '#f4f4f5', stroke: '#52525b' }, // Medium Zinc
                            { fill: '#e4e4e7', stroke: '#3f3f46' }, // Dark Zinc
                            { fill: '#fafafa', stroke: '#71717a' }, // Light Zinc
                            { fill: '#f4f4f5', stroke: '#52525b' }, // Medium Zinc
                            { fill: '#e4e4e7', stroke: '#3f3f46' }, // Dark Zinc
                            { fill: '#fafafa', stroke: '#71717a' }  // Light Zinc
                        ];

                        bengkuluKota.forEach((kota, index) => {
                            const colorIndex = index % bengkuluColors.length;
                            const kotaStyle = {
                                fillColor: bengkuluColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: bengkuluColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: bengkuluColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: bengkuluColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `bengkulu_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        bengkuluKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Bengkulu</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    bengkuluKotaLayers.push(kotaLayer);
                                    bengkuluKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    bengkuluKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Bengkulu GeoJSON:', error);
                    });

                // Load and display Gorontalo boundary
                fetch('/geojson/gorontalo.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Gorontalo style dengan warna fuchsia/magenta
                        const gorontaloBoundaryStyle = {
                            fillColor: '#d946ef',
                            fillOpacity: 0.25,
                            color: '#c026d3',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const gorontaloHoverStyle = {
                            fillColor: '#d946ef',
                            fillOpacity: 0.4,
                            color: '#a21caf',
                            weight: 4,
                            opacity: 1
                        };

                        gorontaloLayer = L.geoJSON(data, {
                            style: gorontaloBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(gorontaloHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Gorontalo</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        gorontaloLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Gorontalo kota/kabupaten boundaries
                        const gorontaloKota = [
                            'Boalemo', 'Bone Bolango', 'Danau Limboto', 'Gorontalo', 'Gorontalo Utara', 'Kota Gorontalo', 'Pohuwato'
                        ];

                        // Warna fuchsia/magenta untuk Gorontalo (konsisten)
                        const gorontaloColors = [
                            { fill: '#fae8ff', stroke: '#d946ef' }, // Light Fuchsia
                            { fill: '#f5d0fe', stroke: '#c026d3' }, // Medium Fuchsia
                            { fill: '#f0abfc', stroke: '#a21caf' }, // Dark Fuchsia
                            { fill: '#fae8ff', stroke: '#d946ef' }, // Light Fuchsia
                            { fill: '#f5d0fe', stroke: '#c026d3' }, // Medium Fuchsia
                            { fill: '#f0abfc', stroke: '#a21caf' }, // Dark Fuchsia
                            { fill: '#fae8ff', stroke: '#d946ef' }  // Light Fuchsia
                        ];

                        gorontaloKota.forEach((kota, index) => {
                            const colorIndex = index % gorontaloColors.length;
                            const kotaStyle = {
                                fillColor: gorontaloColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: gorontaloColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: gorontaloColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: gorontaloColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `gorontalo_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        gorontaloKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Gorontalo</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    gorontaloKotaLayers.push(kotaLayer);
                                    gorontaloKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    gorontaloKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Gorontalo GeoJSON:', error);
                    });

                // Load and display Jambi boundary
                fetch('/geojson/jambi.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Jambi style dengan warna rose/pink
                        const jambiBoundaryStyle = {
                            fillColor: '#f43f5e',
                            fillOpacity: 0.25,
                            color: '#e11d48',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const jambiHoverStyle = {
                            fillColor: '#f43f5e',
                            fillOpacity: 0.4,
                            color: '#be123c',
                            weight: 4,
                            opacity: 1
                        };

                        jambiLayer = L.geoJSON(data, {
                            style: jambiBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(jambiHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Jambi</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        jambiLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Jambi kota/kabupaten boundaries
                        const jambiKota = [
                            'Batang Hari', 'Bungo', 'Jambi', 'Kerinci', 'Merangin', 'Muaro Jambi', 'Sarolangun', 'Sungai Penuh', 'Tanjung Jabung Barat', 'Tanjung Jabung Timur', 'Tebo'
                        ];

                        // Warna rose/pink untuk Jambi (konsisten)
                        const jambiColors = [
                            { fill: '#ffe4e6', stroke: '#f43f5e' }, // Light Rose
                            { fill: '#fecdd3', stroke: '#e11d48' }, // Medium Rose
                            { fill: '#fda4af', stroke: '#be123c' }, // Dark Rose
                            { fill: '#ffe4e6', stroke: '#f43f5e' }, // Light Rose
                            { fill: '#fecdd3', stroke: '#e11d48' }, // Medium Rose
                            { fill: '#fda4af', stroke: '#be123c' }, // Dark Rose
                            { fill: '#ffe4e6', stroke: '#f43f5e' }, // Light Rose
                            { fill: '#fecdd3', stroke: '#e11d48' }, // Medium Rose
                            { fill: '#fda4af', stroke: '#be123c' }, // Dark Rose
                            { fill: '#ffe4e6', stroke: '#f43f5e' }, // Light Rose
                            { fill: '#fecdd3', stroke: '#e11d48' }  // Medium Rose
                        ];

                        jambiKota.forEach((kota, index) => {
                            const colorIndex = index % jambiColors.length;
                            const kotaStyle = {
                                fillColor: jambiColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: jambiColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: jambiColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: jambiColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `jambi_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        jambiKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Jambi</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    jambiKotaLayers.push(kotaLayer);
                                    jambiKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    jambiKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Jambi GeoJSON:', error);
                    });

                // Load and display Kalimantan Barat boundary
                fetch('/geojson/kalimantan_barat.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kalimantan Barat style dengan warna violet/indigo
                        const kalimantanBaratBoundaryStyle = {
                            fillColor: '#8b5cf6',
                            fillOpacity: 0.25,
                            color: '#7c3aed',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kalimantanBaratHoverStyle = {
                            fillColor: '#8b5cf6',
                            fillOpacity: 0.4,
                            color: '#6d28d9',
                            weight: 4,
                            opacity: 1
                        };

                        kalimantanBaratLayer = L.geoJSON(data, {
                            style: kalimantanBaratBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kalimantanBaratHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kalimantan Barat</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kalimantanBaratLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kalimantan Barat kota/kabupaten boundaries
                        const kalimantanBaratKota = [
                            'Bengkayang', 'Kapuas Hulu', 'Kayong Utara', 'Ketapang', 'Kota Pontianak', 'Kubu Raya', 'Landak', 'Melawi', 'Pontianak', 'Sambas', 'Sanggau', 'Sekadau', 'Singkawang', 'Sintang'
                        ];

                        // Warna violet/indigo untuk Kalimantan Barat (konsisten)
                        const kalimantanBaratColors = [
                            { fill: '#ede9fe', stroke: '#8b5cf6' }, // Light Violet
                            { fill: '#ddd6fe', stroke: '#7c3aed' }, // Medium Violet
                            { fill: '#c4b5fd', stroke: '#6d28d9' }, // Dark Violet
                            { fill: '#ede9fe', stroke: '#8b5cf6' }, // Light Violet
                            { fill: '#ddd6fe', stroke: '#7c3aed' }, // Medium Violet
                            { fill: '#c4b5fd', stroke: '#6d28d9' }, // Dark Violet
                            { fill: '#ede9fe', stroke: '#8b5cf6' }, // Light Violet
                            { fill: '#ddd6fe', stroke: '#7c3aed' }, // Medium Violet
                            { fill: '#c4b5fd', stroke: '#6d28d9' }, // Dark Violet
                            { fill: '#ede9fe', stroke: '#8b5cf6' }, // Light Violet
                            { fill: '#ddd6fe', stroke: '#7c3aed' }, // Medium Violet
                            { fill: '#c4b5fd', stroke: '#6d28d9' }, // Dark Violet
                            { fill: '#ede9fe', stroke: '#8b5cf6' }, // Light Violet
                            { fill: '#ddd6fe', stroke: '#7c3aed' }  // Medium Violet
                        ];

                        kalimantanBaratKota.forEach((kota, index) => {
                            const colorIndex = index % kalimantanBaratColors.length;
                            const kotaStyle = {
                                fillColor: kalimantanBaratColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kalimantanBaratColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kalimantanBaratColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kalimantanBaratColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `kalbar_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        kalimantanBaratKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kalimantan Barat</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kalimantanBaratKotaLayers.push(kotaLayer);
                                    kalimantanBaratKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kalimantanBaratKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kalimantan Barat GeoJSON:', error);
                    });

                // Load and display Kalimantan Selatan boundary
                fetch('/geojson/kalimantan_selatan.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kalimantan Selatan style dengan warna sky blue
                        const kalimantanSelatanBoundaryStyle = {
                            fillColor: '#0ea5e9',
                            fillOpacity: 0.25,
                            color: '#0284c7',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kalimantanSelatanHoverStyle = {
                            fillColor: '#0ea5e9',
                            fillOpacity: 0.4,
                            color: '#0369a1',
                            weight: 4,
                            opacity: 1
                        };

                        kalimantanSelatanLayer = L.geoJSON(data, {
                            style: kalimantanSelatanBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kalimantanSelatanHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kalimantan Selatan</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kalimantanSelatanLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kalimantan Selatan kota/kabupaten boundaries
                        const kalimantanSelatanKota = [
                            'Balangan', 'Banjar', 'Banjar Baru', 'Banjarmasin', 'Barito Kuala', 'Hulu Sungai Selatan', 'Hulu Sungai Tengah', 'Hulu Sungai Utara', 'Kota Baru', 'Tabalong', 'Tanah Bumbu', 'Tanah Laut', 'Tapin'
                        ];

                        // Warna sky blue untuk Kalimantan Selatan (konsisten)
                        const kalimantanSelatanColors = [
                            { fill: '#e0f2fe', stroke: '#0ea5e9' }, // Light Sky Blue
                            { fill: '#bae6fd', stroke: '#0284c7' }, // Medium Sky Blue
                            { fill: '#7dd3fc', stroke: '#0369a1' }, // Dark Sky Blue
                            { fill: '#e0f2fe', stroke: '#0ea5e9' }, // Light Sky Blue
                            { fill: '#bae6fd', stroke: '#0284c7' }, // Medium Sky Blue
                            { fill: '#7dd3fc', stroke: '#0369a1' }, // Dark Sky Blue
                            { fill: '#e0f2fe', stroke: '#0ea5e9' }, // Light Sky Blue
                            { fill: '#bae6fd', stroke: '#0284c7' }, // Medium Sky Blue
                            { fill: '#7dd3fc', stroke: '#0369a1' }, // Dark Sky Blue
                            { fill: '#e0f2fe', stroke: '#0ea5e9' }, // Light Sky Blue
                            { fill: '#bae6fd', stroke: '#0284c7' }, // Medium Sky Blue
                            { fill: '#7dd3fc', stroke: '#0369a1' }, // Dark Sky Blue
                            { fill: '#e0f2fe', stroke: '#0ea5e9' }  // Light Sky Blue
                        ];

                        kalimantanSelatanKota.forEach((kota, index) => {
                            const colorIndex = index % kalimantanSelatanColors.length;
                            const kotaStyle = {
                                fillColor: kalimantanSelatanColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kalimantanSelatanColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kalimantanSelatanColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kalimantanSelatanColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `kalsel_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        kalimantanSelatanKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kalimantan Selatan</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kalimantanSelatanKotaLayers.push(kotaLayer);
                                    kalimantanSelatanKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kalimantanSelatanKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kalimantan Selatan GeoJSON:', error);
                    });

                // Load and display Kalimantan Tengah boundary
                fetch('/geojson/kalimantan_tengah.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kalimantan Tengah style dengan warna blue
                        const kalimantanTengahBoundaryStyle = {
                            fillColor: '#3b82f6',
                            fillOpacity: 0.25,
                            color: '#2563eb',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kalimantanTengahHoverStyle = {
                            fillColor: '#3b82f6',
                            fillOpacity: 0.4,
                            color: '#1d4ed8',
                            weight: 4,
                            opacity: 1
                        };

                        kalimantanTengahLayer = L.geoJSON(data, {
                            style: kalimantanTengahBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kalimantanTengahHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kalimantan Tengah</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kalimantanTengahLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kalimantan Tengah kota/kabupaten boundaries
                        const kalimantanTengahKota = [
                            'Barito Selatan', 'Barito Timur', 'Barito Utara', 'Gunung Mas', 'Kapuas', 'Katingan', 'Kotawaringin Barat', 'Kotawaringin Timur', 'Lamandau', 'Murung Raya', 'Palangka Raya', 'Pulang Pisau', 'Seruyan', 'Sukamara'
                        ];

                        // Warna blue untuk Kalimantan Tengah (konsisten)
                        const kalimantanTengahColors = [
                            { fill: '#dbeafe', stroke: '#3b82f6' }, // Light Blue
                            { fill: '#bfdbfe', stroke: '#2563eb' }, // Medium Blue
                            { fill: '#93c5fd', stroke: '#1d4ed8' }, // Dark Blue
                            { fill: '#dbeafe', stroke: '#3b82f6' }, // Light Blue
                            { fill: '#bfdbfe', stroke: '#2563eb' }, // Medium Blue
                            { fill: '#93c5fd', stroke: '#1d4ed8' }, // Dark Blue
                            { fill: '#dbeafe', stroke: '#3b82f6' }, // Light Blue
                            { fill: '#bfdbfe', stroke: '#2563eb' }, // Medium Blue
                            { fill: '#93c5fd', stroke: '#1d4ed8' }, // Dark Blue
                            { fill: '#dbeafe', stroke: '#3b82f6' }, // Light Blue
                            { fill: '#bfdbfe', stroke: '#2563eb' }, // Medium Blue
                            { fill: '#93c5fd', stroke: '#1d4ed8' }, // Dark Blue
                            { fill: '#dbeafe', stroke: '#3b82f6' }, // Light Blue
                            { fill: '#bfdbfe', stroke: '#2563eb' }  // Medium Blue
                        ];

                        kalimantanTengahKota.forEach((kota, index) => {
                            const colorIndex = index % kalimantanTengahColors.length;
                            const kotaStyle = {
                                fillColor: kalimantanTengahColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kalimantanTengahColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kalimantanTengahColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kalimantanTengahColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `kalteng_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        kalimantanTengahKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kalimantan Tengah</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kalimantanTengahKotaLayers.push(kotaLayer);
                                    kalimantanTengahKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kalimantanTengahKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kalimantan Tengah GeoJSON:', error);
                    });

                // Load and display Kalimantan Timur boundary
                fetch('/geojson/kalimantan_timur.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kalimantan Timur style dengan warna navy/dark blue
                        const kalimantanTimurBoundaryStyle = {
                            fillColor: '#1e40af',
                            fillOpacity: 0.25,
                            color: '#1e3a8a',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kalimantanTimurHoverStyle = {
                            fillColor: '#1e40af',
                            fillOpacity: 0.4,
                            color: '#1e3a8a',
                            weight: 4,
                            opacity: 1
                        };

                        kalimantanTimurLayer = L.geoJSON(data, {
                            style: kalimantanTimurBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kalimantanTimurHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kalimantan Timur</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kalimantanTimurLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kalimantan Timur kota/kabupaten boundaries
                        const kalimantanTimurKota = [
                            'Balikpapan', 'Berau', 'Bontang', 'Kutai Barat', 'Kutai Kartanegara', 'Kutai Timur', 'Paser', 'Penajam Paser Utara', 'Samarinda'
                        ];

                        // Warna navy/dark blue untuk Kalimantan Timur (konsisten)
                        const kalimantanTimurColors = [
                            { fill: '#dbeafe', stroke: '#1e40af' }, // Light Navy
                            { fill: '#bfdbfe', stroke: '#1e3a8a' }, // Medium Navy
                            { fill: '#93c5fd', stroke: '#1e3a8a' }, // Dark Navy
                            { fill: '#dbeafe', stroke: '#1e40af' }, // Light Navy
                            { fill: '#bfdbfe', stroke: '#1e3a8a' }, // Medium Navy
                            { fill: '#93c5fd', stroke: '#1e3a8a' }, // Dark Navy
                            { fill: '#dbeafe', stroke: '#1e40af' }, // Light Navy
                            { fill: '#bfdbfe', stroke: '#1e3a8a' }, // Medium Navy
                            { fill: '#93c5fd', stroke: '#1e3a8a' }  // Dark Navy
                        ];

                        kalimantanTimurKota.forEach((kota, index) => {
                            const colorIndex = index % kalimantanTimurColors.length;
                            const kotaStyle = {
                                fillColor: kalimantanTimurColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kalimantanTimurColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kalimantanTimurColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kalimantanTimurColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `kaltim_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        kalimantanTimurKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kalimantan Timur</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kalimantanTimurKotaLayers.push(kotaLayer);
                                    kalimantanTimurKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kalimantanTimurKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kalimantan Timur GeoJSON:', error);
                    });

                // Load and display Kalimantan Utara boundary
                fetch('/geojson/kalimantan_utara.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kalimantan Utara style dengan warna indigo
                        const kalimantanUtaraBoundaryStyle = {
                            fillColor: '#6366f1',
                            fillOpacity: 0.25,
                            color: '#4f46e5',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kalimantanUtaraHoverStyle = {
                            fillColor: '#6366f1',
                            fillOpacity: 0.4,
                            color: '#4338ca',
                            weight: 4,
                            opacity: 1
                        };

                        kalimantanUtaraLayer = L.geoJSON(data, {
                            style: kalimantanUtaraBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kalimantanUtaraHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kalimantan Utara</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kalimantanUtaraLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kalimantan Utara kota/kabupaten boundaries
                        const kalimantanUtaraKota = [
                            { name: 'Bulungan', file: 'bulungan.geojson' },
                            { name: 'Malinau', file: 'malinau.geojson' },
                            { name: 'Nunukan', file: 'nunukan.geojson' },
                            { name: 'Tana Tidung', file: 'tana_tidung.geojson' },
                            { name: 'Tarakan', file: 'tarakan.geojson' }
                        ];

                        // Warna indigo untuk Kalimantan Utara (konsisten)
                        const kalimantanUtaraColors = [
                            { fill: '#e0e7ff', stroke: '#6366f1' }, // Light Indigo
                            { fill: '#c7d2fe', stroke: '#4f46e5' }, // Medium Indigo
                            { fill: '#a5b4fc', stroke: '#4338ca' }, // Dark Indigo
                            { fill: '#e0e7ff', stroke: '#6366f1' }, // Light Indigo
                            { fill: '#c7d2fe', stroke: '#4f46e5' }  // Medium Indigo
                        ];

                        kalimantanUtaraKota.forEach((kotaObj, index) => {
                            const kota = kotaObj.name;
                            const colorIndex = index % kalimantanUtaraColors.length;
                            const kotaStyle = {
                                fillColor: kalimantanUtaraColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kalimantanUtaraColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kalimantanUtaraColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kalimantanUtaraColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = kotaObj.file;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kotaObj.name} (${fileName})`);
                                        kalimantanUtaraKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kotaObj.name;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kalimantan Utara</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kalimantanUtaraKotaLayers.push(kotaLayer);
                                    kalimantanUtaraKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kalimantanUtaraKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kalimantan Utara GeoJSON:', error);
                    });

                // Load and display Kepulauan Riau boundary
                fetch('/geojson/kepulauan_riau.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Kepulauan Riau style dengan warna purple/violet
                        const kepulauanRiauBoundaryStyle = {
                            fillColor: '#a855f7',
                            fillOpacity: 0.25,
                            color: '#9333ea',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const kepulauanRiauHoverStyle = {
                            fillColor: '#a855f7',
                            fillOpacity: 0.4,
                            color: '#7e22ce',
                            weight: 4,
                            opacity: 1
                        };

                        kepulauanRiauLayer = L.geoJSON(data, {
                            style: kepulauanRiauBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(kepulauanRiauHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Kepulauan Riau</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        kepulauanRiauLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Kepulauan Riau kota/kabupaten boundaries
                        const kepulauanRiauKota = [
                            'Batam', 'Bintan', 'Karimun', 'Kepulauan Anambas', 'Lingga', 'Natuna', 'Tanjungpinang'
                        ];

                        // Warna purple/violet untuk Kepulauan Riau (konsisten)
                        const kepulauanRiauColors = [
                            { fill: '#f3e8ff', stroke: '#a855f7' }, // Light Purple
                            { fill: '#e9d5ff', stroke: '#9333ea' }, // Medium Purple
                            { fill: '#d8b4fe', stroke: '#7e22ce' }, // Dark Purple
                            { fill: '#f3e8ff', stroke: '#a855f7' }, // Light Purple
                            { fill: '#e9d5ff', stroke: '#9333ea' }, // Medium Purple
                            { fill: '#d8b4fe', stroke: '#7e22ce' }, // Dark Purple
                            { fill: '#f3e8ff', stroke: '#a855f7' }  // Light Purple
                        ];

                        kepulauanRiauKota.forEach((kota, index) => {
                            const colorIndex = index % kepulauanRiauColors.length;
                            const kotaStyle = {
                                fillColor: kepulauanRiauColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: kepulauanRiauColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: kepulauanRiauColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: kepulauanRiauColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `kepri_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        kepulauanRiauKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Kepulauan Riau</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    kepulauanRiauKotaLayers.push(kotaLayer);
                                    kepulauanRiauKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    kepulauanRiauKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Kepulauan Riau GeoJSON:', error);
                    });

                // Load and display Lampung boundary
                fetch('/geojson/lampung.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Lampung style dengan warna orange
                        const lampungBoundaryStyle = {
                            fillColor: '#f97316',
                            fillOpacity: 0.25,
                            color: '#ea580c',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const lampungHoverStyle = {
                            fillColor: '#f97316',
                            fillOpacity: 0.4,
                            color: '#c2410c',
                            weight: 4,
                            opacity: 1
                        };

                        lampungLayer = L.geoJSON(data, {
                            style: lampungBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(lampungHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Lampung</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        lampungLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Lampung kota/kabupaten boundaries
                        const lampungKota = [
                            'Bandar Lampung', 'Lampung Barat', 'Lampung Selatan', 'Lampung Tengah', 'Lampung Timur', 'Lampung Utara', 'Mesuji', 'Metro', 'Pesawaran', 'Pringsewu', 'Tanggamus', 'Tulang Bawang Barat', 'Tulangbawang', 'Way Kanan'
                        ];

                        // Warna orange untuk Lampung (konsisten)
                        const lampungColors = [
                            { fill: '#ffedd5', stroke: '#f97316' }, // Light Orange
                            { fill: '#fed7aa', stroke: '#ea580c' }, // Medium Orange
                            { fill: '#fdba74', stroke: '#c2410c' }, // Dark Orange
                            { fill: '#ffedd5', stroke: '#f97316' }, // Light Orange
                            { fill: '#fed7aa', stroke: '#ea580c' }, // Medium Orange
                            { fill: '#fdba74', stroke: '#c2410c' }, // Dark Orange
                            { fill: '#ffedd5', stroke: '#f97316' }, // Light Orange
                            { fill: '#fed7aa', stroke: '#ea580c' }, // Medium Orange
                            { fill: '#fdba74', stroke: '#c2410c' }, // Dark Orange
                            { fill: '#ffedd5', stroke: '#f97316' }, // Light Orange
                            { fill: '#fed7aa', stroke: '#ea580c' }, // Medium Orange
                            { fill: '#fdba74', stroke: '#c2410c' }, // Dark Orange
                            { fill: '#ffedd5', stroke: '#f97316' }, // Light Orange
                            { fill: '#fed7aa', stroke: '#ea580c' }  // Medium Orange
                        ];

                        lampungKota.forEach((kota, index) => {
                            const colorIndex = index % lampungColors.length;
                            const kotaStyle = {
                                fillColor: lampungColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: lampungColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: lampungColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: lampungColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `lampung_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        lampungKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Lampung</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    lampungKotaLayers.push(kotaLayer);
                                    lampungKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    lampungKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Lampung GeoJSON:', error);
                    });

                // Load and display Maluku boundary
                fetch('/geojson/maluku.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Maluku style dengan warna teal/cyan
                        const malukuBoundaryStyle = {
                            fillColor: '#14b8a6',
                            fillOpacity: 0.25,
                            color: '#0d9488',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const malukuHoverStyle = {
                            fillColor: '#14b8a6',
                            fillOpacity: 0.4,
                            color: '#0f766e',
                            weight: 4,
                            opacity: 1
                        };

                        malukuLayer = L.geoJSON(data, {
                            style: malukuBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(malukuHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Maluku</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        malukuLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Maluku kota/kabupaten boundaries
                        const malukuKota = [
                            'Ambon', 'Buru', 'Buru Selatan', 'Kepulauan Aru', 'Maluku Barat Daya', 'Maluku Tengah', 'Maluku Tenggara', 'Maluku Tenggara Barat', 'Seram Bagian Barat', 'Seram Bagian Timur', 'Tual'
                        ];

                        // Warna teal/cyan untuk Maluku (konsisten)
                        const malukuColors = [
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Light Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }  // Medium Teal
                        ];

                        malukuKota.forEach((kota, index) => {
                            const colorIndex = index % malukuColors.length;
                            const kotaStyle = {
                                fillColor: malukuColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: malukuColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: malukuColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: malukuColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `maluku_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        malukuKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Maluku</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    malukuKotaLayers.push(kotaLayer);
                                    malukuKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    malukuKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Maluku GeoJSON:', error);
                    });

                // Load and display Maluku Utara boundary
                fetch('/geojson/maluku_utara.geojson')
                    .then(response => response.json())
                    .then(data => {
                        // Maluku Utara style dengan warna turquoise (beda tapi masih serasi)
                        const malukuUtaraBoundaryStyle = {
                            fillColor: '#22d3ee',
                            fillOpacity: 0.25,
                            color: '#0ea5e9',
                            weight: 3,
                            opacity: 0.8,
                            dashArray: '10, 5'
                        };

                        const malukuUtaraHoverStyle = {
                            fillColor: '#22d3ee',
                            fillOpacity: 0.4,
                            color: '#0284c7',
                            weight: 4,
                            opacity: 1
                        };

                        malukuUtaraLayer = L.geoJSON(data, {
                            style: malukuUtaraBoundaryStyle,
                            onEachFeature: function(feature, layer) {
                                layer.on({
                                    mouseover: function(e) {
                                        const layer = e.target;
                                        layer.setStyle(malukuUtaraHoverStyle);
                                        layer.bindPopup(`<div class="text-center"><strong>Maluku Utara</strong><br>Batas Wilayah</div>`).openPopup();
                                    },
                                    mouseout: function(e) {
                                        malukuUtaraLayer.resetStyle(e.target);
                                    },
                                    click: function(e) {
                                        map.fitBounds(e.target.getBounds());
                                    }
                                });
                            }
                        }).addTo(map);

                        // Load and display all Maluku Utara kota/kabupaten boundaries
                        const malukuUtaraKota = [
                            'Halmahera Barat',
                            'Halmahera Selatan',
                            'Halmahera Tengah',
                            'Halmahera Timur',
                            'Halmahera Utara',
                            'Kepulauan Sula',
                            'Pulau Morotai',
                            'Ternate',
                            'Tidore Kepulauan'
                        ];

                        // Warna turquoise/sky teal untuk Maluku Utara (konsisten dan beda dari Maluku)
                        const malukuUtaraColors = [
                            { fill: '#e0f2fe', stroke: '#22d3ee' }, // Light Turquoise
                            { fill: '#bae6fd', stroke: '#0ea5e9' }, // Sky
                            { fill: '#7dd3fc', stroke: '#0284c7' }, // Deeper Sky
                            { fill: '#ccfbf1', stroke: '#14b8a6' }, // Soft Teal
                            { fill: '#99f6e4', stroke: '#0d9488' }, // Medium Teal
                            { fill: '#5eead4', stroke: '#0f766e' }, // Dark Teal
                            { fill: '#e0f2fe', stroke: '#22d3ee' }, // Light Turquoise
                            { fill: '#bae6fd', stroke: '#0ea5e9' }, // Sky
                            { fill: '#7dd3fc', stroke: '#0284c7' }  // Deeper Sky
                        ];

                        malukuUtaraKota.forEach((kota, index) => {
                            const colorIndex = index % malukuUtaraColors.length;
                            const kotaStyle = {
                                fillColor: malukuUtaraColors[colorIndex].fill,
                                fillOpacity: 0.3,
                                color: malukuUtaraColors[colorIndex].stroke,
                                weight: 2.5,
                                opacity: 0.8,
                                dashArray: '8, 4'
                            };

                            const kotaHoverStyle = {
                                fillColor: malukuUtaraColors[colorIndex].fill,
                                fillOpacity: 0.5,
                                color: malukuUtaraColors[colorIndex].stroke,
                                weight: 4,
                                opacity: 1,
                                dashArray: '0'
                            };

                            const fileName = `malut_${kota.replace(/ /g, '_')}.geojson`;
                            fetch(`/geojson/${fileName}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (!data || !data.features || data.features.length === 0) {
                                        console.warn(`Empty or invalid GeoJSON for ${kota} (${fileName})`);
                                        malukuUtaraKotaLoadedCount++;
                                        fitAllBounds();
                                        return;
                                    }
                                    
                                    const kotaLayer = L.geoJSON(data, {
                                        style: kotaStyle,
                                        onEachFeature: function(feature, layer) {
                                            // Get nama dari feature properties jika ada, atau gunakan nama kota
                                            const namaKota = feature.properties?.NAME_2 || feature.properties?.name || kota;
                                            layer.bindPopup(`<div class="text-center py-2"><strong class="text-lg">${namaKota}</strong><br><span class="text-sm text-gray-600">Maluku Utara</span></div>`, {
                                                className: 'custom-popup'
                                            });
                                            
                                            layer.on({
                                                mouseover: function(e) {
                                                    const layer = e.target;
                                                    layer.setStyle(kotaHoverStyle);
                                                    layer.openPopup();
                                                },
                                                mouseout: function(e) {
                                                    kotaLayer.resetStyle(e.target);
                                                    layer.closePopup();
                                                },
                                                click: function(e) {
                                                    map.fitBounds(e.target.getBounds(), { padding: [80, 80], maxZoom: 11 });
                                                }
                                            });
                                        }
                                    }).addTo(map);

                                    malukuUtaraKotaLayers.push(kotaLayer);
                                    malukuUtaraKotaLoadedCount++;

                                    // Check if all layers are loaded
                                    fitAllBounds();
                                })
                                .catch(error => {
                                    console.error(`Error loading ${kota} (${fileName}):`, error);
                                    malukuUtaraKotaLoadedCount++;
                                    fitAllBounds();
                                });
                        });
                    })
                    .catch(error => {
                        console.error('Error loading Maluku Utara GeoJSON:', error);
                    });

                // Initial view is set to Indonesia center, will be adjusted when all layers loaded
                // Update coordinates to Indonesia center
                document.getElementById('coordinates').textContent = '-2.0000, 118.0000';
            })
            .catch(error => {
                console.error('Error loading GeoJSON:', error);
                alert('Gagal memuat data batas wilayah');
            });

        // Custom marker icon with pastel colors
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background: linear-gradient(135deg, #f9a8d4, #c084fc); width: 30px; height: 30px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div><div style="position: absolute; top: 10px; left: 10px; transform: rotate(45deg); width: 10px; height: 10px; background: white; border-radius: 50%;"></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 30]
        });

        // Add default marker
        let marker = L.marker([-6.3, 106.1], { icon: customIcon })
            .addTo(map)
            .bindPopup('<div class="text-center"><strong>Banten</strong><br>Pusat peta</div>');

        // Update info on map click
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(4);
            const lng = e.latlng.lng.toFixed(4);
            
            // Update marker position
            marker.setLatLng(e.latlng);
            marker.setPopupContent(`<div class="text-center"><strong>Koordinat</strong><br>${lat}, ${lng}</div>`).openPopup();
            
            // Update info cards
            document.getElementById('coordinates').textContent = `${lat}, ${lng}`;
        });

        // Update zoom level
        map.on('zoomend', function() {
            document.getElementById('zoomLevel').textContent = map.getZoom();
        });
        
        // Initial zoom level update
        document.getElementById('zoomLevel').textContent = map.getZoom();

        // Search functionality (basic geocoding using Nominatim)
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value;
                if (query.trim() !== '') {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                const lat = parseFloat(data[0].lat);
                                const lon = parseFloat(data[0].lon);
                                map.setView([lat, lon], 15);
                                marker.setLatLng([lat, lon]);
                                marker.setPopupContent(`<div class="text-center"><strong>${data[0].display_name}</strong></div>`).openPopup();
                                document.getElementById('coordinates').textContent = `${lat.toFixed(4)}, ${lon.toFixed(4)}`;
                                document.getElementById('locationInfo').textContent = data[0].display_name.substring(0, 50) + (data[0].display_name.length > 50 ? '...' : '');
                            } else {
                                alert('Lokasi tidak ditemukan');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mencari lokasi');
                        });
                }
            }
        });

        // Add custom CSS for marker
        const style = document.createElement('style');
        style.textContent = `
            .custom-marker {
                background: transparent !important;
                border: none !important;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

