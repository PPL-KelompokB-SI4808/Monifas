# SOP Pengerjaan Modul MONIFAS

Dokumen ini wajib dibaca setiap anggota sebelum mengerjakan modul. Aturan teknis lengkap ada di [AGENTS.md](AGENTS.md)

## 1. Pembagian Modul

| # | Modul (kebab) | Namespace | Submodul |
|---|---|---|---|
| 1 | `users-and-access` | `UsersAndAccess` | `users`, `roles`, `user-addresses` |
| 2 | `facility` | `Facility` | `buildings`, `rooms`, `facilities` |
| 3 | `location-and-category` | `LocationAndCategory` | `locations`, `facility-categories`, `damage-categories` |
| 4 | `reporting` | `Reporting` | `reports`, `report-evidence`, `report-priorities` |
| 5 | `maintenance` | `Maintenance` | `officers`, `assignments`, `schedules` |
| 6 | `repair` | `Repair` | `repairs`, `materials`, `costs` |
| 7 | `monitoring-and-analytic` | `MonitoringAndAnalytic` | `dashboard`, `maintenance-report`, `sla-monitoring` |
| 8 | `supporting` | `Supporting` | `announcements`, `feedback`, `campaigns` |

Penanggung jawab: isi sendiri di tabel ini saat pembagian tugas.

## 2. Alur Kerja Git

1. `git pull origin main` sebelum mulai.
2. Buat branch dari main: `feature/<modul>/<submodul>-<deskripsi>` (contoh: `feature/facility/buildings-crud`).
3. Commit kecil dan sering, format: `feat(facility): tambah CRUD gedung` (`feat`, `fix`, `refactor`, `test`, `docs`, `style`).
4. Jalankan checklist di bagian 9 sebelum membuat Pull Request.
5. Minimal 1 anggota lain me-review sebelum merge. Jangan push langsung ke `main`.
6. Hanya ubah file dalam modul sendiri. Perubahan di file shared (lihat bagian 8) harus dikoordinasikan.

## 3. Struktur File per Submodul

Ganti `<Namespace>`, `<modul>`, `<submodul>` sesuai tabel di atas (PascalCase di `app/` dan `tests/`, kebab-case di `resources/views/` dan `routes/`).

| Jenis | Lokasi |
|---|---|
| Controller | `app/Http/Controllers/<Namespace>/<Submodul>/` |
| Form Request | `app/Http/Requests/<Namespace>/<Submodul>/` |
| Service | `app/Services/<Namespace>/<Submodul>/` |
| Model | `app/Models/` (datar, tanpa subfolder) |
| DTO / Enum / Constant | `app/DTOs`, `app/Enums`, `app/Constants` |
| Route | `routes/modules/<modul>.php` |
| View halaman | `resources/views/pages/dashboard/<modul>/<submodul>/` |
| Komponen khusus submodul | `resources/views/pages/dashboard/<modul>/<submodul>/components/` |
| Partial submodul | `resources/views/pages/dashboard/<modul>/<submodul>/partials/` |
| Feature test | `tests/Feature/<Namespace>/<Submodul>/` |
| Unit test | `tests/Unit/<Namespace>/<Submodul>/` |
| Migration / Factory / Seeder | `database/migrations`, `database/factories`, `database/seeders` |

> Catatan: struktur view mengikuti kondisi repo saat ini (`resources/views/pages/...`). Jika ada perbedaan dengan `AGENTS.md`, ikuti struktur yang sudah ada di repo dan laporkan ke tim agar dokumen disamakan.

Contoh hasil akhir untuk `facility/buildings`:

```
app/Http/Controllers/Facility/Buildings/BuildingController.php
app/Http/Requests/Facility/Buildings/StoreBuildingRequest.php
app/Http/Requests/Facility/Buildings/UpdateBuildingRequest.php
app/Services/Facility/Buildings/BuildingService.php
app/Models/Building.php
routes/modules/facility.php
resources/views/pages/dashboard/facility/buildings/index.blade.php
resources/views/pages/dashboard/facility/buildings/create.blade.php
resources/views/pages/dashboard/facility/buildings/components/building-table.blade.php
resources/views/pages/dashboard/facility/buildings/components/building-form.blade.php
tests/Feature/Facility/Buildings/BuildingCrudTest.php
tests/Unit/Facility/Buildings/BuildingServiceTest.php
```

## 4. Aturan Backend

- Alur: `Route → Controller → Service → View`. Tidak ada logika bisnis atau query di controller maupun Blade.
- Controller tipis: terima request, panggil Service, kembalikan view/redirect.
- Validasi dan otorisasi input hanya lewat Form Request. Dilarang `$request->validate()` di controller.
- Query Eloquent dan logika bisnis ditaruh di Service.
- Gunakan Enum untuk nilai tetap (status, prioritas, tipe). Jangan hardcode string di banyak tempat.
- Gunakan DTO jika data yang dikirim Controller ke Service lebih dari beberapa field.
- Gunakan Carbon bawaan Laravel untuk tanggal. Dilarang menambah package validasi atau tanggal pihak ketiga.
- Route modul diletakkan di `routes/modules/<modul>.php`. Jangan menetapkan prefix `auth` atau `dashboard` tanpa persetujuan tim.
- Relasi antar-modul lewat Model Eloquent. Jangan memanggil Service modul lain secara langsung tanpa koordinasi dengan pemilik modul tersebut.

## 5. Aturan Frontend (Blade, Komponen, Styling)

### 5.1 Hierarki penggunaan komponen

Sebelum menulis markup baru, cek berurutan:

1. **Komponen UI dasar** di `resources/views/components/ui/` (`<x-ui.button>`, `<x-ui.card>`, `<x-ui.badge>`, `<x-ui.eyebrow>`, dst.). **Wajib dipakai** jika tersedia.
2. **Komponen shared lintas modul** di `resources/views/components/shared/` (`<x-shared.*>`), untuk komponen yang dipakai di 2 modul atau lebih.
3. **Komponen khusus halaman/submodul** di folder `components/` milik halaman itu sendiri, untuk komponen yang hanya dipakai di sana.
4. Baru buat komponen baru jika tidak ada yang cocok.

### 5.2 Aturan penempatan komponen

| Situasi | Lokasi |
|---|---|
| Elemen dasar generik (button, input, modal, table, dst.) | `components/ui/` |
| Dipakai ≥ 2 modul, bukan elemen dasar (mis. status-badge, empty-state, page-header) | `components/shared/` |
| Dipakai hanya di 1 submodul | `pages/dashboard/<modul>/<submodul>/components/` |
| Kerangka layout (sidebar, navbar, header) | `components/layouts/` |

Dilarang menaruh komponen khusus submodul di `components/ui/` atau `components/shared/`. Dilarang menyalin markup yang sama ke banyak view. Jika sudah muncul untuk kedua kalinya, jadikan komponen.

### 5.3 Mendaftarkan komponen khusus submodul

Komponen di folder `components/` halaman diakses lewat namespace anonymous component. Contoh yang sudah ada: `resources/views/pages/home/components` terdaftar sebagai namespace `home` di [AppServiceProvider.php](app/Providers/AppServiceProvider.php).

```php
// app/Providers/AppServiceProvider.php, method boot()
Blade::anonymousComponentPath(
    resource_path('views/pages/dashboard/facility/buildings/components'),
    'facility-buildings'
);
```

Pemakaian di view:

```blade
<x-facility-buildings::building-table :buildings="$buildings" />
```

Aturan penamaan namespace: `<modul>-<submodul>` dalam kebab-case. Tambahkan registrasi hanya untuk submodul sendiri.

### 5.4 Standar menulis komponen

- Anonymous component Blade saja (tanpa PHP class). File kebab-case, contoh `building-table.blade.php`.
- Deklarasikan `@props([...])` dengan default. Gunakan `$attributes->merge([...])` agar class bisa ditambahkan dari luar.
- Gunakan `{{ $slot }}` dan named slot untuk konten fleksibel.
- Komponen hanya menerima data lewat props. Jangan query database di dalam komponen.
- Ikuti pola di [button.blade.php](resources/views/components/ui/button.blade.php).

### 5.5 Struktur halaman

- Halaman dibungkus layout: `<x-layouts.app> ... </x-layouts.app>`.
- File halaman (`index`, `create`, `edit`, `show`) hanya merakit komponen. Markup panjang dipecah menjadi komponen di `components/`.
- Contoh acuan: [pages/home/index.blade.php](resources/views/pages/home/index.blade.php) yang hanya berisi `<x-home::hero />`, `<x-home::coverage />`, dst.

### 5.6 Styling

- Tailwind CSS 4 via Vite. Konfigurasi di `resources/css/app.css`. Jangan membuat `tailwind.config.*`.
- **Wajib memakai design token**: `bg-primary`, `text-primary-foreground`, `bg-secondary`, `text-foreground`, `bg-background`, `border-border`, dst.
- Dilarang hardcode warna (`bg-blue-500`, `#ff0000`, dst.) dan dilarang inline `style=""` untuk warna.
- Pastikan tampilan nyaman di mobile dan desktop (responsive) serta di mode `.dark`.
- Ikon memakai `mallardduck/blade-lucide-icons`. Jangan menambah library ikon lain.

### 5.7 Interaktivitas

- Gunakan Alpine.js 3. Logika yang panjang dipisah ke `resources/js/components/<modul>-<submodul>.js`, lalu daftarkan di `resources/js/app.js`.
- Grafik memakai Chart.js 4. Dilarang menambah framework JS (React, Vue, jQuery, dst.).
- Tidak ada API layer atau SPA. Halaman di-render server-side.

## 6. Langkah Mengerjakan Satu Submodul

Urutan yang disarankan:

1. **Baca spec**: requirements, design, dan tasks untuk submodul.
2. **Database**: buat migration, Model (`app/Models/`), Factory, dan Seeder bila perlu.
3. **Enum/DTO**: tentukan nilai tetap dan struktur data.
4. **Form Request**: buat `Store...Request` dan `Update...Request`.
5. **Service**: implementasikan logika bisnis dan query.
6. **Controller**: tipis, panggil Service.
7. **Route**: tambah di `routes/modules/<modul>.php`. Pastikan file dimuat dari route utama.
8. **View**: rakit halaman dari komponen `ui`, `shared`, lalu komponen khusus submodul.
9. **Test**: Feature test untuk alur HTTP (sukses, validasi gagal, akses tanpa izin) dan Unit test untuk logika Service.
10. **Verifikasi**: jalankan checklist bagian 9.

## 7. Penamaan

| Objek | Konvensi | Contoh |
|---|---|---|
| Model | PascalCase tunggal | `Building` |
| Tabel | snake_case jamak | `buildings` |
| Controller | `<Entitas>Controller` | `BuildingController` |
| Form Request | `Store<Entitas>Request`, `Update<Entitas>Request` | `StoreBuildingRequest` |
| Service | `<Entitas>Service` | `BuildingService` |
| Enum | PascalCase | `ReportStatus` |
| Route name | `<modul>.<submodul>.<aksi>` | `facility.buildings.index` |
| File view / komponen | kebab-case | `building-table.blade.php` |
| Test | `<Fitur>Test` | `BuildingCrudTest` |

## 8. Area Shared (Koordinasi Dulu)

Perubahan pada file berikut berdampak ke semua modul. Diskusikan di grup dan buat PR terpisah:

- `resources/css/app.css` (design token)
- `resources/views/components/ui/*` dan `components/shared/*`
- `resources/views/components/layouts/*`
- `app/Providers/*`
- `routes/web.php`
- `resources/js/app.js`
- `composer.json`, `package.json`, `phpstan.neon`, `pint.json`, `vite.config.js`
- File yang sudah ada milik modul lain

Menambah komponen baru ke `ui` atau `shared` boleh, selama tidak mengubah komponen yang sudah ada dan tidak ada duplikasi. Jika perlu varian baru pada komponen yang ada, tambahkan lewat prop, jangan mengubah perilaku default.

Dilarang menimpa file yang sudah ada milik orang lain. Gunakan `.gitkeep` untuk direktori kosong.

## 9. Checklist Sebelum Pull Request

```sh
./vendor/bin/pint            # rapikan code style
./vendor/bin/pint --test     # pastikan lolos
./vendor/bin/phpstan analyse # Larastan level >= 5
php artisan test             # Pest, semua harus hijau
npm run build                # pastikan build frontend sukses
```

Centang manual:

- [ ] File PHP baru tidak memakai `declare(strict_types=1);`
- [ ] Controller tipis, tidak ada query/logika bisnis
- [ ] Validasi lewat Form Request
- [ ] Logika bisnis di Service
- [ ] Model di `app/Models/` (datar)
- [ ] Memakai komponen `<x-ui.*>` yang sudah ada, tidak membuat ulang
- [ ] Komponen khusus submodul ada di `components/` milik submodul, bukan di `ui` atau `shared`
- [ ] Tidak ada markup yang disalin berulang antar view
- [ ] Hanya memakai design token, tidak ada warna hardcode
- [ ] Tampilan responsif dan aman di dark mode
- [ ] Ada Feature test dan Unit test
- [ ] Tidak ada `dd()`, `dump()`, `console.log`, atau kode yang di-comment tanpa alasan
- [ ] Tidak mengubah file shared tanpa koordinasi
- [ ] Deskripsi PR menjelaskan apa yang dikerjakan dan cara mengujinya

## 10. Menjalankan Proyek

```sh
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
composer run dev   # Laravel server + Vite
```

## 11. Larangan Ringkas

- Menaruh logika bisnis di Controller atau Blade.
- Membuat `app/Models/<Namespace>/`.
- Menambah package pihak ketiga tanpa persetujuan tim.
- Membuat `tailwind.config.*`.
- Warna hardcode, `style=""` inline untuk warna.
- Menyalin komponen ke banyak tempat.
- Mengubah file shared atau milik modul lain tanpa koordinasi.
- Push langsung ke `main`.
