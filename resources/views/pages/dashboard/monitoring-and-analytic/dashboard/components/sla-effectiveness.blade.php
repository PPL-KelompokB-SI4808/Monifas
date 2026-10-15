<div class="flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-xs">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-foreground sm:text-base">
            Efektivitas Performa SLA
        </h3>
        <span class="inline-flex items-center rounded-sm bg-emerald-50 px-2 py-0.5 text-[10px] font-bold tracking-wider text-emerald-600 uppercase border border-emerald-100">
            Target Tercapai
        </span>
    </div>

    <!-- Gauge / Circular Progress -->
    <div class="my-4 flex flex-col items-center justify-center">
        <div class="relative flex items-center justify-center">
            <!-- Circular Progress SVG -->
            <svg class="h-36 w-36 -rotate-90 transform" viewBox="0 0 120 120">
                <circle
                    cx="60"
                    cy="60"
                    r="50"
                    class="text-muted/40 stroke-current"
                    stroke-width="10"
                    fill="transparent"
                ></circle>
                <circle
                    cx="60"
                    cy="60"
                    r="50"
                    class="text-primary stroke-current transition-all duration-1000 ease-out"
                    stroke-width="10"
                    stroke-dasharray="314.159"
                    stroke-dashoffset="18.22"
                    stroke-linecap="round"
                    fill="transparent"
                ></circle>
            </svg>

            <!-- Center Text -->
            <div class="absolute flex flex-col items-center justify-center text-center">
                <span class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                    94.2%
                </span>
                <span class="text-[10px] font-medium text-muted-foreground uppercase tracking-wider">
                    SLA Met Rate
                </span>
            </div>
        </div>
    </div>

    <!-- Performance Details Cards -->
    <div class="space-y-2.5">
        <!-- Metric 1: Respon -->
        <div class="flex items-center justify-between rounded-xl border border-blue-100/80 bg-blue-50/60 p-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <x-lucide-clock class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-semibold text-foreground">Waktu Respon Rata-rata</div>
                    <div class="text-[10px] text-muted-foreground">Target Internal &lt; 60 menit</div>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold text-foreground">42</span>
                <span class="text-xs font-medium text-muted-foreground">Menit</span>
            </div>
        </div>

        <!-- Metric 2: Resolusi -->
        <div class="flex items-center justify-between rounded-xl border border-purple-100/80 bg-purple-50/60 p-3">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                    <x-lucide-wrench class="h-4 w-4" />
                </div>
                <div>
                    <div class="text-xs font-semibold text-foreground">Waktu Resolusi Rata-rata</div>
                    <div class="text-[10px] text-muted-foreground">Penanganan tuntas di tempat</div>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold text-foreground">3.8</span>
                <span class="text-xs font-medium text-muted-foreground">Jam</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-4 border-t border-border/60 pt-3 text-center text-[11px] text-muted-foreground">
        Tingkat kepuasan penyewa: <span class="font-semibold text-foreground">4.8 / 5.0</span>
    </div>
</div>
