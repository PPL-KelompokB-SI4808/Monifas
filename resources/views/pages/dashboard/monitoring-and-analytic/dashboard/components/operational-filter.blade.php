@props([
    'lastSync' => '2 Menit lalu',
])

<div class="rounded-xl border border-border bg-card p-4 shadow-xs sm:p-5">
    <!-- Header -->
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-3">
        <div class="flex items-center gap-2">
            <x-lucide-filter class="h-4 w-4 text-primary" />
            <span class="text-xs font-bold tracking-wider text-foreground uppercase">
                Filter Parameter Operasional
            </span>
        </div>
        <div class="text-[11px] text-muted-foreground">
            Terakhir disinkronkan: <span class="font-medium text-foreground">{{ $lastSync }}</span>
        </div>
    </div>

    <!-- Filter Form -->
    <form @submit.prevent class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 items-end">
        <!-- Kawasan / Wilayah -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-medium text-muted-foreground">
                Kawasan / Wilayah
            </label>
            <div class="relative">
                <select class="w-full appearance-none rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Wilayah</option>
                    <option value="zona-timur">Zona Timur</option>
                    <option value="zona-barat">Zona Barat</option>
                    <option value="zona-pusat">Zona Pusat</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>

        <!-- Gedung / Sektor -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-medium text-muted-foreground">
                Gedung / Sektor
            </label>
            <div class="relative">
                <select class="w-full appearance-none rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Gedung</option>
                    <option value="gedung-utama">Gedung Utama A</option>
                    <option value="tower-logistik">Tower Logistik B</option>
                    <option value="ruang-powerhouse">Ruang Powerhouse</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>

        <!-- Kategori Fasilitas -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-medium text-muted-foreground">
                Kategori Fasilitas
            </label>
            <div class="relative">
                <select class="w-full appearance-none rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Kategori</option>
                    <option value="hvac">HVAC & Pendingin</option>
                    <option value="elektrikal">Elektrikal & Power</option>
                    <option value="plumbing">Plumbing & Sanitasi</option>
                    <option value="lift">Lift & Eskalator</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>

        <!-- Status Laporan -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-medium text-muted-foreground">
                Status Laporan
            </label>
            <div class="relative">
                <select class="w-full appearance-none rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="baru">Laporan Baru</option>
                    <option value="proses">Dalam Proses</option>
                    <option value="investigasi">Investigasi</option>
                    <option value="selesai">Selesai</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>

        <!-- Penanggung Jawab -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-medium text-muted-foreground">
                Penanggung Jawab
            </label>
            <div class="relative">
                <select class="w-full appearance-none rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Tim Teknisi</option>
                    <option value="tim-a">Tim Shift A (Budi)</option>
                    <option value="tim-b">Tim Shift B (Rian)</option>
                    <option value="tim-c">Tim Shift C (Dedi)</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
            <button
                type="submit"
                class="flex-1 inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-xs transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-ring cursor-pointer"
            >
                Terapkan
            </button>
            <button
                type="button"
                title="Reset / Muat Ulang"
                class="inline-flex items-center justify-center rounded-lg border border-border bg-background p-2 text-muted-foreground transition hover:bg-secondary hover:text-foreground focus:outline-none focus:ring-1 focus:ring-ring cursor-pointer"
            >
                <x-lucide-rotate-cw class="h-4 w-4" />
            </button>
        </div>
    </form>
</div>
