<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
            SLA Performance & Real-time Monitoring
        </h1>
        <p class="mt-1 text-xs text-muted-foreground sm:text-sm">
            Pemantauan kepatuhan Service Level Agreement perbaikan & pemeliharaan fasilitas secara real-time
        </p>
    </div>

    <!-- Filter Controls & Download -->
    <div class="flex flex-wrap items-center gap-2.5">
        <!-- Periode Filter -->
        <div class="relative">
            <select class="appearance-none rounded-lg border border-border bg-background py-1.5 pl-3 pr-8 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                <option value="okt-2024">Bulan Ini (Oktober 2024)</option>
                <option value="sep-2024">September 2024</option>
                <option value="agu-2024">Agustus 2024</option>
            </select>
            <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
        </div>

        <!-- Wilayah / Gedung Filter -->
        <div class="relative">
            <select class="appearance-none rounded-lg border border-border bg-background py-1.5 pl-3 pr-8 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                <option value="">Semua Wilayah / Gedung</option>
                <option value="gedung-pusat">Gedung Pusat</option>
                <option value="tower-b">Tower B</option>
                <option value="gedung-operasional">Gedung Operasional</option>
                <option value="gedung-utama">Gedung Utama</option>
            </select>
            <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
        </div>

        <!-- Unduh Laporan Button -->
        <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-semibold text-primary-foreground shadow-xs transition hover:bg-primary/90 cursor-pointer"
        >
            <x-lucide-download class="h-3.5 w-3.5" />
            Unduh Laporan
        </button>
    </div>
</div>
