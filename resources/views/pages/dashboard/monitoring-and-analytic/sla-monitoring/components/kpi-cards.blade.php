<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Card 1: SLA Compliance Rate -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between">
                <span class="text-xs font-medium text-muted-foreground">SLA Compliance Rate</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200/60">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Di Atas Target (95%)
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black tracking-tight text-foreground sm:text-3xl">96.8%</span>
                <span class="text-xs font-semibold text-emerald-600 flex items-center gap-0.5">
                    &uarr; +1.2% bln lalu
                </span>
            </div>
        </div>
        <!-- Indicator Bar -->
        <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-muted">
            <div class="h-full rounded-full bg-emerald-500" style="width: 96.8%"></div>
        </div>
    </div>

    <!-- Card 2: Tiket Tepat Waktu -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between">
                <span class="text-xs font-medium text-muted-foreground">Tiket Tepat Waktu</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                    <x-lucide-check-circle-2 class="h-4 w-4" />
                </div>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-black tracking-tight text-foreground sm:text-3xl">412</span>
                <span class="text-xs text-muted-foreground ml-1">/ 426 total tiket</span>
            </div>
        </div>
        <div class="mt-4 text-xs text-muted-foreground">
            Rata-rata resolusi: <span class="font-bold text-foreground">2j 14m</span>
        </div>
    </div>

    <!-- Card 3: Mendekati Batas (At Risk) -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between">
                <span class="text-xs font-medium text-muted-foreground">Mendekati Batas (At Risk)</span>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200/60">
                    Butuh Atensi
                </span>
            </div>
            <div class="mt-2">
                <span class="text-2xl font-black tracking-tight text-amber-600 sm:text-3xl">8</span>
                <span class="text-xs text-muted-foreground ml-1">Tiket aktif</span>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs text-amber-600 font-semibold">
            <x-lucide-clock class="h-3.5 w-3.5" />
            <span>4 tiket &lt; 30 menit</span>
        </div>
    </div>

    <!-- Card 4: Terlambat (SLA Breached) -->
    <div class="rounded-xl border border-border bg-card p-5 shadow-xs transition hover:shadow-md flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between">
                <span class="text-xs font-medium text-muted-foreground">Terlambat (SLA Breached)</span>
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-rose-50 text-rose-600 border border-rose-100">
                    <x-lucide-alert-circle class="h-4 w-4" />
                </div>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-black tracking-tight text-rose-600 sm:text-3xl">14</span>
                <span class="text-xs font-medium text-rose-500">3.2% dari total</span>
            </div>
        </div>
        <div class="mt-4 text-xs text-muted-foreground">
            Mayoritas kendala suku cadang pending
        </div>
    </div>
</div>
