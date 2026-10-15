<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Card 1: Total Biaya Bulan Ini -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Total Biaya Bulan Ini</span>
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                <x-lucide-banknote class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                Rp 148.500.000
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
            <x-lucide-trending-down class="h-3.5 w-3.5" />
            <span>-4.2% dari anggaran pagu</span>
        </div>
    </div>

    <!-- Card 2: Rata-rata MTTR (Penyelesaian) -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Rata-rata MTTR (Penyelesaian)</span>
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                <x-lucide-clock class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                2 Jam 45 Menit
            </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
            <span class="inline-flex items-center rounded-sm bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200/60">
                Target SLA: &lt; 4 Jam
            </span>
            <span class="inline-flex items-center rounded-sm bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200/60">
                96.8% Patuh
            </span>
        </div>
    </div>

    <!-- Card 3: Rasio Preventif vs Korektif -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Rasio Preventif vs Korektif</span>
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 border border-purple-100">
                <x-lucide-bar-chart-2 class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2 flex items-baseline justify-between">
            <div>
                <span class="text-xl font-bold text-foreground sm:text-2xl">68%</span>
                <span class="text-xs text-muted-foreground ml-1">Preventif</span>
            </div>
            <div>
                <span class="text-base font-bold text-amber-500 sm:text-lg">32%</span>
                <span class="text-xs text-muted-foreground ml-1">Korektif</span>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="mt-4 flex h-2 w-full overflow-hidden rounded-full bg-muted">
            <div class="h-full bg-blue-600 rounded-l-full" style="width: 68%"></div>
            <div class="h-full bg-amber-500 rounded-r-full" style="width: 32%"></div>
        </div>
    </div>

    <!-- Card 4: Total Tiket Bulan Ini -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium text-muted-foreground">Total Tiket Bulan Ini</span>
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600 border border-purple-100">
                <x-lucide-ticket class="h-4 w-4" />
            </div>
        </div>
        <div class="mt-2">
            <div class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                142 <span class="text-xs font-normal text-muted-foreground">Pekerjaan</span>
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between text-xs">
            <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="font-bold text-foreground">134</span>
                <span class="text-muted-foreground">Selesai</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                <span class="font-bold text-foreground">8</span>
                <span class="text-muted-foreground">Dalam Pengerjaan</span>
            </div>
        </div>
    </div>
</div>
