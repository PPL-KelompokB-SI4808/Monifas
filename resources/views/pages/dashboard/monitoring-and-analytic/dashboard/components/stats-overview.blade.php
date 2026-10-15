<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Card 1: Total Laporan Aktif -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Total Laporan Aktif</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 border border-blue-100/80">
                <x-lucide-file-text class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">128</div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-600 border border-emerald-200/50">
                <x-lucide-trending-up class="h-3 w-3" />
                +4.8% bln ini
            </span>
            <!-- Sparkline SVG -->
            <svg class="h-6 w-20 text-blue-500 overflow-visible" fill="none" viewBox="0 0 80 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 18 Q 20 20, 35 15 T 60 10 T 78 5" />
            </svg>
        </div>
    </div>

    <!-- Card 2: Laporan Selesai -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Laporan Selesai</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100/80">
                <x-lucide-check-circle-2 class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">1,042</div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-600 border border-emerald-200/50">
                <x-lucide-trending-up class="h-3 w-3" />
                +12.4% bln ini
            </span>
            <!-- Sparkline SVG -->
            <svg class="h-6 w-20 text-emerald-500 overflow-visible" fill="none" viewBox="0 0 80 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 20 Q 25 18, 40 12 T 65 8 T 78 4" />
            </svg>
        </div>
    </div>

    <!-- Card 3: Fasilitas Bermasalah Kritis -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Fasilitas Bermasalah Kritis</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 border border-rose-100/80">
                <x-lucide-alert-triangle class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-rose-600 sm:text-3xl">9 Unit</div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-600 border border-rose-200/50">
                <x-lucide-trending-down class="h-3 w-3" />
                -2 unit dari kmrn
            </span>
            <!-- Sparkline SVG -->
            <svg class="h-6 w-20 text-rose-500 overflow-visible" fill="none" viewBox="0 0 80 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 6 Q 20 8, 38 14 T 60 18 T 78 20" />
            </svg>
        </div>
    </div>

    <!-- Card 4: Maintenance Berjalan -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Maintenance Berjalan</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-100/80">
                <x-lucide-zap class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">34 SPK</div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 border border-amber-200/50">
                14 Teknisi aktif
            </span>
            <!-- Sparkline SVG -->
            <svg class="h-6 w-20 text-amber-500 overflow-visible" fill="none" viewBox="0 0 80 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 16 Q 15 22, 30 14 T 55 12 T 78 18" />
            </svg>
        </div>
    </div>
</div>
