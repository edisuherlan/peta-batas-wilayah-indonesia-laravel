# 🗺️ Peta Batas Wilayah Indonesia

Aplikasi peta interaktif untuk menampilkan batas wilayah provinsi dan kota/kabupaten di Indonesia menggunakan Laravel 12, Leaflet.js, dan OpenStreetMap.

![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 📋 Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Cara Penggunaan](#cara-penggunaan)
- [Struktur Project](#struktur-project)
- [Daftar Provinsi yang Tersedia](#daftar-provinsi-yang-tersedia)
- [Kontribusi](#kontribusi)
- [Tentang Pembuat](#tentang-pembuat)
- [Lisensi](#lisensi)

## 🎯 Tentang Project

Aplikasi ini dibuat untuk memudahkan visualisasi batas-batas wilayah administratif di Indonesia. Dengan tampilan yang modern dan interaktif, kamu bisa melihat batas provinsi dan kota/kabupaten dengan mudah. Setiap provinsi memiliki warna pastel yang berbeda untuk memudahkan identifikasi, dan saat kamu hover atau klik pada area tertentu, akan muncul informasi detail.

Project ini cocok untuk:
- 🔍 Eksplorasi peta Indonesia secara interaktif
- 📚 Pembelajaran geografi Indonesia
- 🎨 Referensi desain peta dengan Leaflet.js
- 💼 Basis untuk aplikasi GIS (Geographic Information System)

## ✨ Fitur Utama

### 🗺️ Visualisasi Peta Interaktif
- Peta Indonesia yang dapat di-zoom dan di-pan dengan smooth
- Tampilan batas provinsi dengan garis putus-putus yang jelas
- Batas kota/kabupaten dengan warna pastel yang konsisten per provinsi

### 🎨 Desain Modern
- UI/UX dengan dominan warna purple/pink yang soft
- Responsive design - bisa digunakan di desktop, tablet, dan mobile
- Hover effects yang smooth saat mouse berada di atas wilayah
- Popup informasi yang informatif dan mudah dibaca

### 🔍 Fitur Interaktif
- **Hover Effect**: Saat mouse di atas wilayah, akan muncul popup dengan nama daerah
- **Click to Zoom**: Klik pada provinsi atau kota untuk zoom otomatis ke area tersebut
- **Search Functionality**: Fitur pencarian lokasi menggunakan OpenStreetMap Nominatim
- **Real-time Info**: Informasi zoom level, koordinat, dan jumlah daerah yang dimuat

### 📊 Informasi yang Ditampilkan
- Nama provinsi dan batas wilayahnya
- Nama kota/kabupaten beserta provinsinya
- Total jumlah kabupaten/kota yang sudah dimuat
- Koordinat lokasi yang sedang dilihat
- Level zoom saat ini

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework modern untuk backend
- **PHP 8.2+** - Bahasa pemrograman server-side

### Frontend
- **Leaflet.js** - Library JavaScript untuk peta interaktif
- **OpenStreetMap** - Sumber data peta open source
- **Tailwind CSS** - Framework CSS utility-first untuk styling
- **Vite** - Build tool modern untuk frontend assets

### Data
- **GeoJSON** - Format data geografis untuk batas wilayah
- Data batas wilayah provinsi dan kota/kabupaten Indonesia

## 📦 Persyaratan Sistem

Sebelum memulai, pastikan kamu sudah menginstall:

- **PHP** >= 8.2
- **Composer** (PHP dependency manager)
- **Node.js** >= 18.x dan **NPM**
- **Web Server** (Apache/Nginx) atau bisa pakai Laragon/XAMPP
- **Database** (opsional, untuk fitur Laravel yang membutuhkan database)

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/edisuherlan/peta-batas-wilayah-indonesia-laravel.git
cd peta-batas-wilayah-indonesia-laravel
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Install Dependencies JavaScript

```bash
npm install
```

### 4. Setup Environment File

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Kemudian generate application key:

```bash
php artisan key:generate
```

### 5. Build Frontend Assets

```bash
npm run build
```

Atau jika ingin development mode dengan hot reload:

```bash
npm run dev
```

### 6. Setup Storage Link (Opsional)

Jika menggunakan storage untuk file upload:

```bash
php artisan storage:link
```

### 7. Jalankan Aplikasi

Jika menggunakan Laragon/XAMPP, cukup akses melalui browser:
```
http://localhost/map
```

Atau jika menggunakan Laravel built-in server:

```bash
php artisan serve
```

Kemudian buka browser di: `http://localhost:8000`

## ⚙️ Konfigurasi

### File GeoJSON

File GeoJSON untuk batas wilayah sudah disertakan di folder `public/geojson/`. File-file ini sudah diorganisir dengan format:

```
public/geojson/
├── banten.geojson
├── jakarta.geojson
├── jawa_barat.geojson
├── ...
└── [nama_provinsi].geojson
```

Untuk kota/kabupaten, formatnya:
```
public/geojson/
├── [prefix_provinsi]_[nama_kota].geojson
└── contoh: banten_Cilegon.geojson
```

### Menambah Provinsi Baru

Untuk menambah provinsi baru, ikuti langkah berikut:

1. **Siapkan file GeoJSON** provinsi dan kota/kabupatennya
2. **Copy file** ke folder `public/geojson/`
3. **Update file** `resources/views/home.blade.php`:
   - Tambahkan variabel layer untuk provinsi baru
   - Tambahkan array kota/kabupaten
   - Tambahkan array warna (pastel colors)
   - Tambahkan fetch untuk memuat GeoJSON
   - Update function `fitAllBounds()` untuk include provinsi baru

Contoh struktur yang perlu ditambahkan:

```javascript
// Deklarasi variabel
let provinsiBaruLayer = null;
let provinsiBaruKotaLayers = [];
let provinsiBaruKotaLoadedCount = 0;

// Array kota/kabupaten
const provinsiBaruKota = ['Kota 1', 'Kota 2', ...];

// Array warna
const provinsiBaruColors = [
    { fill: '#warna1', stroke: '#warna2' },
    // ... lebih banyak warna
];

// Fetch dan render GeoJSON
fetch('/geojson/provinsi_baru.geojson')
    .then(response => response.json())
    .then(data => {
        // ... kode untuk render
    });
```

## 📖 Cara Penggunaan

### Navigasi Peta

1. **Zoom In/Out**: Gunakan scroll mouse atau tombol +/- di peta
2. **Pan (Geser)**: Klik dan drag untuk memindahkan view peta
3. **Click pada Wilayah**: Klik pada provinsi atau kota untuk auto-zoom ke area tersebut
4. **Hover**: Arahkan mouse ke wilayah untuk melihat informasi popup

### Mencari Lokasi

1. Ketik nama lokasi di search box di bagian atas
2. Tekan Enter
3. Peta akan otomatis zoom ke lokasi yang dicari

### Info Cards

Di bawah peta ada 3 info card:
- **Wilayah**: Menampilkan daftar provinsi yang sudah dimuat dan total kabupaten/kota
- **Zoom Level**: Menampilkan level zoom saat ini
- **Koordinat**: Menampilkan koordinat lokasi yang sedang dilihat

## 📁 Struktur Project

```
peta-batas-wilayah-indonesia-laravel/
├── app/                          # Folder aplikasi Laravel
│   ├── Http/
│   │   └── Controllers/
│   │       └── HomeController.php  # Controller untuk halaman utama
│   └── ...
├── public/                       # Folder public (web root)
│   ├── geojson/                 # File GeoJSON untuk batas wilayah
│   │   ├── banten.geojson
│   │   ├── jakarta.geojson
│   │   └── ...
│   ├── build/                   # File hasil build (auto-generated)
│   └── index.php
├── resources/
│   ├── views/
│   │   └── home.blade.php      # View utama dengan peta interaktif
│   ├── css/
│   │   └── app.css             # File CSS utama
│   └── js/
│       └── app.js               # File JavaScript utama
├── routes/
│   └── web.php                  # Route definitions
├── .env                         # Environment configuration (jangan di-commit!)
├── .env.example                 # Contoh file environment
├── composer.json                # PHP dependencies
├── package.json                 # JavaScript dependencies
├── vite.config.js              # Konfigurasi Vite
└── README.md                    # File ini
```

## 🗺️ Daftar Provinsi yang Tersedia

Aplikasi ini sudah termasuk batas wilayah untuk provinsi berikut:

1. ✅ **Banten** (8 kota/kabupaten)
2. ✅ **Jakarta** (6 kota/kabupaten)
3. ✅ **Jawa Barat** (27 kota/kabupaten)
4. ✅ **Jawa Tengah** (36 kota/kabupaten)
5. ✅ **Jawa Timur** (38 kota/kabupaten)
6. ✅ **Yogyakarta** (5 kota/kabupaten)
7. ✅ **Bali** (9 kota/kabupaten)
8. ✅ **Nusa Tenggara Barat** (10 kota/kabupaten)
9. ✅ **Nusa Tenggara Timur** (21 kota/kabupaten)
10. ✅ **Papua** (29 kota/kabupaten)
11. ✅ **Papua Barat** (11 kota/kabupaten)
12. ✅ **Aceh** (23 kota/kabupaten)
13. ✅ **Bangka Belitung** (7 kota/kabupaten)
14. ✅ **Bengkulu** (10 kota/kabupaten)
15. ✅ **Gorontalo** (7 kota/kabupaten)
16. ✅ **Jambi** (11 kota/kabupaten)
17. ✅ **Kalimantan Barat** (14 kota/kabupaten)
18. ✅ **Kalimantan Selatan** (13 kota/kabupaten)
19. ✅ **Kalimantan Tengah** (14 kota/kabupaten)
20. ✅ **Kalimantan Timur** (9 kota/kabupaten)
21. ✅ **Kalimantan Utara** (5 kota/kabupaten)
22. ✅ **Kepulauan Riau** (7 kota/kabupaten)
23. ✅ **Lampung** (14 kota/kabupaten)
24. ✅ **Maluku** (11 kota/kabupaten)
25. ✅ **Maluku Utara** (9 kota/kabupaten)
26. ✅ **Riau** (12 kota/kabupaten)
27. ✅ **Sulawesi Barat** (5 kota/kabupaten)
28. ✅ **Sumatera Barat** (20 kota/kabupaten)
29. ✅ **Sumatera Selatan** (15 kota/kabupaten)

**Total: 29 provinsi dengan lebih dari 415 kota/kabupaten** 🎉

> **Catatan**: Provinsi lainnya masih dalam pengembangan. Kontribusi untuk menambah provinsi lainnya sangat diterima!

## 🤝 Kontribusi

Kontribusi sangat diterima! Jika kamu ingin menambahkan fitur atau memperbaiki bug:

1. **Fork** repository ini
2. **Buat branch** baru (`git checkout -b fitur/namafitur`)
3. **Commit** perubahan kamu (`git commit -m 'Menambah fitur baru'`)
4. **Push** ke branch (`git push origin fitur/namafitur`)
5. **Buat Pull Request**

### Prioritas Pengembangan
- [ ] Menambahkan provinsi yang belum ada (Sulawesi, Sumatra lainnya, dll)
- [ ] Optimasi performa loading GeoJSON
- [ ] Menambahkan fitur export peta
- [ ] Menambahkan legend untuk warna provinsi
- [ ] Dark mode theme
- [ ] Fitur pencarian yang lebih advanced

## 👨‍💻 Tentang Pembuat

**Edi Suherlan**

- 📧 Email: [edisuherlan@gmail.com](mailto:edisuherlan@gmail.com)
- 🌐 Website: [audhighasu.com](https://audhighasu.com)
- 💻 GitHub: [@edisuherlan](https://github.com/edisuherlan)

Aplikasi ini dibuat dengan ❤️ menggunakan Laravel 12, Leaflet.js, dan OpenStreetMap.

## 📄 Lisensi

Project ini menggunakan lisensi [MIT License](LICENSE). Artinya kamu bebas untuk:
- ✅ Menggunakan project ini untuk keperluan komersial
- ✅ Memodifikasi sesuai kebutuhan
- ✅ Mendistribusikan
- ✅ Menggunakan secara private

Dengan catatan tetap mencantumkan credit kepada pembuat asli.

## 🙏 Terima Kasih

Terima Kasih sudah menggunakan aplikasi ini! Jika aplikasi ini membantu kamu, jangan lupa:

- ⭐ **Star** repository ini di GitHub
- 🐛 **Report** bug jika menemukan masalah
- 💡 **Suggest** fitur baru yang ingin ditambahkan
- 📢 **Share** ke teman-teman yang mungkin membutuhkan

---

**Happy Mapping! 🗺️✨**
