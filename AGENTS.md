# MONIFAS — Panduan AI untuk Repository

MONIFAS (Platform Pelaporan & Monitoring Fasilitas Infrastruktur) adalah aplikasi web **Laravel server-rendered monolith**. Sumber kebenaran ada di `.kiro/specs/monifas-base-project/` (`requirements.md`, `design.md`, `tasks.md`). Baca spec tersebut sebelum membuat keputusan arsitektur.

## Tech Stack

| Aspek | Teknologi |
|---|---|
| Framework / Bahasa | Laravel 13+, PHP 8.3+ |
| View | Blade + Blade Components internal (`<x-ui.*>`) |
| Interaktivitas | Alpine.js 3 (di-bundle Vite) |
| Styling | Tailwind CSS 4 via Vite (konfigurasi di `resources/css/app.css`, tanpa `tailwind.config.*`) |
| Grafik / Ikon | Chart.js 4 / `mallardduck/blade-lucide-icons` |
| Validasi / Tanggal | Form Request + Validation bawaan / Carbon bawaan |
| Kualitas kode | Laravel Pint, Larastan (PHPStan level ≥ 5) |
| Testing | Pest + pest-plugin-laravel |
| Package manager | Composer, npm |

Alur request: `Router → Controller → Service → View (Blade)`, validasi lewat Form Request. Tidak ada API layer, SPA, atau WebSocket.

## Aturan Wajib

- Proyek ini **tidak memakai** `declare(strict_types=1);`. Jangan menambahkannya ke file PHP.
- Controller tipis; logika bisnis di `app/Services/`; validasi di `app/Http/Requests/`.
- Model diletakkan **datar** di `app/Models/` (jangan buat `app/Models/<Namespace>/`).
- Gunakan Form Request, Validation, dan Carbon bawaan Laravel; jangan tambah package validasi/tanggal pihak ketiga.
- Warna UI memakai design token (`bg-primary`, `text-primary-foreground`, `bg-secondary`, `text-foreground`, `bg-background`, dst.) yang didefinisikan di `:root` dan `.dark` pada `resources/css/app.css`.
- Komponen UI = Blade anonymous component di `resources/views/components/ui/` (tanpa PHP class).
- Layout utama: `resources/views/layouts/app.blade.php` (memuat `@vite(['resources/css/app.css', 'resources/js/app.js'])`).
- Route per modul diletakkan di `routes/modules/`; jangan menetapkan prefix `auth`/`dashboard` tanpa persetujuan.
- Gunakan `.gitkeep` untuk direktori kosong; jangan menimpa file yang sudah ada.


## Struktur Modul

Delapan modul, masing-masing dengan submodul. Kebab-case dipakai di `resources/views/dashboard/`, PascalCase di `app/` dan `tests/`.

| Modul (kebab) | Namespace | Submodul | Kegunaan modul |
|---|---|---|---|
| `users-and-access` | `UsersAndAccess` | `users`, `roles`, `user-addresses` | Manajemen akun pengguna, hak akses berbasis peran, dan alamat pengguna |
| `facility` | `Facility` | `buildings`, `rooms`, `facilities` | Inventaris aset fisik: gedung, ruangan, dan fasilitas di dalamnya |
| `location-and-category` | `LocationAndCategory` | `locations`, `facility-categories`, `damage-categories` | Data master lokasi dan klasifikasi (kategori fasilitas & kerusakan) |
| `reporting` | `Reporting` | `reports`, `report-evidence`, `report-priorities` | Pelaporan kerusakan oleh pengguna, bukti (foto/dokumen), dan penentuan prioritas |
| `maintenance` | `Maintenance` | `officers`, `assignments`, `schedules` | Petugas, penugasan laporan ke petugas, dan penjadwalan perawatan |
| `repair` | `Repair` | `repairs`, `materials`, `costs` | Pencatatan perbaikan, material yang dipakai, dan biaya |
| `monitoring-and-analytic` | `MonitoringAndAnalytic` | `dashboard`, `maintenance-report`, `sla-monitoring` | Monitoring analitik performa fasilitas, laporan perawatan, dan kepatuhan SLA |
| `supporting` | `Supporting` | `announcements`, `feedback`, `campaigns` | Fitur pendukung: pengumuman, umpan balik, dan kampanye |

### Kegunaan Submodul

| Submodul | Kegunaan |
|---|---|
| `users` | CRUD akun pengguna & autentikasi profil |
| `roles` | Definisi peran dan izin akses |
| `user-addresses` | Alamat/lokasi domisili milik pengguna |
| `buildings` | Data gedung |
| `rooms` | Data ruangan di dalam gedung |
| `facilities` | Data fasilitas/aset per ruangan beserta kondisinya |
| `locations` | Master lokasi (area/wilayah) |
| `facility-categories` | Master kategori fasilitas |
| `damage-categories` | Master kategori/jenis kerusakan |
| `reports` | Laporan kerusakan dan status/siklus hidupnya |
| `report-evidence` | Lampiran bukti laporan |
| `report-priorities` | Tingkat prioritas/urgensi laporan |
| `officers` | Data petugas pemeliharaan/teknisi |
| `assignments` | Penugasan laporan ke petugas |
| `schedules` | Jadwal perawatan/perbaikan |
| `repairs` | Catatan pekerjaan perbaikan |
| `materials` | Material/suku cadang yang digunakan |
| `costs` | Biaya perbaikan |
| `dashboard` | Dashboard analitik utama dan tren kerusakan fasilitas |
| `maintenance-report` | Rekapitulasi laporan pemeliharaan dan statistik perbaikan aset |
| `sla-monitoring` | Pemantauan waktu tanggap dan kepatuhan Service Level Agreement (SLA) |
| `announcements` | Pengumuman ke pengguna |
| `feedback` | Masukan/penilaian pengguna |
| `campaigns` | Kampanye/sosialisasi |

Untuk setiap `<modul>/<submodul>` lokasi file:

- Controller: `app/Http/Controllers/<Namespace>/<submodul>/` — menerima request, memanggil Service, mengembalikan view/redirect (tipis).
- Form Request: `app/Http/Requests/<Namespace>/<submodul>/` — validasi & otorisasi input.
- Service: `app/Services/<Namespace>/<submodul>/` — logika bisnis dan akses data.
- View: `resources/views/dashboard/<modul>/<submodul>/partials/` — potongan Blade khusus submodul (tabel, form, dsb.).
- Test: `tests/Feature/<Namespace>/<submodul>/` (alur HTTP/end-to-end) dan `tests/Unit/<Namespace>/<submodul>/` (logika terisolasi).

### Direktori Shared

| Direktori | Kegunaan |
|---|---|
| `app/Models/` | Model Eloquent (datar, tanpa subfolder namespace) |
| `app/DTOs` | Objek pembawa data antar lapisan (Controller ↔ Service) |
| `app/Enums` | Enum PHP untuk nilai tetap (status, tipe, prioritas, dsb.) |
| `app/Constants` | Konstanta aplikasi yang dipakai lintas modul |
| `app/Support` | Helper/utilitas umum yang tidak terikat modul |
| `app/Providers` | Service provider Laravel |
| `app/Console/Commands` | Perintah Artisan kustom (mis. `ScaffoldCommand` untuk generate struktur modul) |
| `resources/views/layouts` | Layout utama halaman (`app.blade.php`) |
| `resources/views/components/ui` | Komponen UI dasar (`<x-ui.*>`: button, input, card, dsb.) |
| `resources/views/components/layout` | Komponen kerangka halaman (sidebar, navbar, header) |
| `resources/views/components/shared` | Komponen bersama lintas modul yang bukan UI dasar |
| `resources/views/auth` | Halaman autentikasi (login, register, dsb.) |
| `resources/views/dashboard` | Halaman dashboard per modul/submodul |
| `resources/js/components` | Komponen Alpine.js |
| `routes/modules` | File route per modul, dimuat dari file route utama |


## Perintah Verifikasi

```sh
composer run dev                  # Laravel server + Vite
php artisan test                  # Pest
./vendor/bin/pint --test          # code style
./vendor/bin/phpstan analyse      # Larastan (level >= 5; paths: app, routes, database)
npm run build                     # hasil di public/build/
php artisan config:cache && php artisan route:cache
```

Konfigurasi tooling: `phpstan.neon`, `pint.json` (preset `laravel`, `declare_strict_types` dinonaktifkan), `.editorconfig` (PHP/Blade 4 spasi; JS/TS/CSS/JSON 2 spasi), `vite.config.js` (`input` = `app.css` + `app.js`, `refresh: true`).

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>
