# MONIFAS

**Platform Pelaporan & Monitoring Fasilitas Infrastruktur**

MONIFAS adalah aplikasi web untuk melaporkan, memantau, dan menangani kerusakan fasilitas infrastruktur (gedung, ruangan, dan aset di dalamnya). Pengguna dapat membuat laporan kerusakan beserta bukti, lalu petugas menindaklanjuti lewat penugasan, penjadwalan, dan pencatatan perbaikan.

## Tech Stack

| Aspek | Teknologi |
|---|---|
| Framework / Bahasa | Laravel 13, PHP 8.3+ |
| View | Blade + Blade Components internal (`<x-ui.*>`) |
| Interaktivitas | Alpine.js 3 |
| Styling | Tailwind CSS 4 via Vite |
| Grafik / Ikon | Chart.js 4 / Blade Lucide Icons |
| Database (default) | SQLite |
| Kualitas kode | Laravel Pint, Larastan (PHPStan) |
| Testing | Pest |

## Modul Aplikasi

| Modul | Submodul | Kegunaan |
|---|---|---|
| `users-and-access` | users, roles, user-addresses | Akun pengguna, hak akses berbasis peran, alamat pengguna |
| `facility` | buildings, rooms, facilities | Inventaris gedung, ruangan, dan fasilitas |
| `location-and-category` | locations, facility-categories, damage-categories | Master lokasi dan kategori fasilitas/kerusakan |
| `reporting` | reports, report-evidence, report-priorities | Laporan kerusakan, bukti, dan prioritas |
| `maintenance` | officers, assignments, schedules | Petugas, penugasan, dan jadwal perawatan |
| `repair` | repairs, materials, costs | Catatan perbaikan, material, dan biaya |
| `supporting` | announcements, feedback, campaigns | Pengumuman, umpan balik, dan kampanye |

Alur request: `Router → Controller → Service → View (Blade)`, dengan validasi lewat Form Request.

## Prasyarat

Pastikan sudah terpasang:

- PHP **8.3** atau lebih baru (ekstensi umum Laravel, termasuk `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`)
- [Composer](https://getcomposer.org) 2
- [Node.js](https://nodejs.org) 20+ beserta npm
- Git

Cek dengan:

```sh
php -v
composer -V
node -v
npm -v
```

## Langkah Startup

### 1. Clone repository

```sh
git clone <url-repository>
cd monifas-laravel
```

### 2. Setup otomatis (disarankan)

```sh
composer run setup
```

Perintah ini menjalankan: `composer install`, menyalin `.env.example` ke `.env` (jika belum ada), `php artisan key:generate`, `php artisan migrate --force`, `npm install`, dan `npm run build`.

### 3. Atau setup manual

```sh
composer install
cp .env.example .env            # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

Jika memakai SQLite dan file database belum ada, `php artisan migrate` akan menawarkan untuk membuatnya. Anda juga bisa membuatnya manual:

```sh
touch database/database.sqlite  # Windows PowerShell: New-Item database\database.sqlite
```

### 4. Jalankan aplikasi

```sh
composer run dev
```

Perintah ini menjalankan server Laravel dan Vite (hot reload) sekaligus. Buka **http://localhost:8000**.

Alternatif, jalankan terpisah di dua terminal:

```sh
php artisan serve
npm run dev
```

> Halaman akan tampil tanpa styling jika Vite belum berjalan. Jalankan `npm run dev` (atau `npm run build` sekali) agar CSS dan JS termuat.

## Konfigurasi Database

Default memakai SQLite. Untuk MySQL/PostgreSQL, ubah di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monifas
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan `php artisan migrate`.

## Perintah Berguna

| Perintah | Fungsi |
|---|---|
| `composer run dev` | Server Laravel + Vite |
| `php artisan test` | Menjalankan test (Pest) |
| `./vendor/bin/pint` | Merapikan code style |
| `./vendor/bin/pint --test` | Cek code style tanpa mengubah file |
| `./vendor/bin/phpstan analyse` | Analisis statis (Larastan) |
| `npm run build` | Build aset produksi ke `public/build/` |
| `php artisan migrate:fresh` | Reset database dan jalankan ulang migrasi |
| `php artisan optimize:clear` | Membersihkan cache config, route, dan view |

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `No application encryption key has been specified` | Jalankan `php artisan key:generate` |
| Halaman tanpa styling / error Vite manifest | Jalankan `npm run dev` atau `npm run build` |
| `could not find driver` | Aktifkan ekstensi `pdo_sqlite` di `php.ini` |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan config:clear` |
| Port 8000 sudah dipakai | Jalankan `php artisan serve --port=8001` dan sesuaikan `APP_URL` |

## Panduan Pengembangan

Proyek ini dikerjakan per modul oleh beberapa anggota tim. Sebelum mulai, baca:

- [SOP-MODUL.md](SOP-MODUL.md): SOP pengerjaan modul (struktur file, komponen, styling, Git, checklist PR)
- [AGENTS.md](AGENTS.md): aturan teknis dan struktur proyek
- `.kiro/specs/monifas-base-project/`: requirements, design, dan tasks

## Lisensi

Proyek ini dibangun di atas [Laravel](https://laravel.com) yang berlisensi [MIT](https://opensource.org/licenses/MIT).
