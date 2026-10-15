<div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
    <div>
        <!-- Header -->
        <div class="border-b border-border/60 pb-3">
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Kalkulasi Skor SLA Keseluruhan
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Agregat kepatuhan tertimbang fasilitas
            </p>
        </div>

        <!-- Gauge / Circular Progress -->
        <div class="my-4 flex flex-col items-center justify-center">
            <div class="relative flex items-center justify-center">
                <!-- Circular Progress SVG -->
                <svg class="h-32 w-32 -rotate-90 transform" viewBox="0 0 120 120">
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
                        stroke-dashoffset="10.05"
                        stroke-linecap="round"
                        fill="transparent"
                    ></circle>
                </svg>

                <!-- Center Text -->
                <div class="absolute flex flex-col items-center justify-center text-center">
                    <span class="text-2xl font-black tracking-tight text-foreground sm:text-3xl">
                        96.8%
                    </span>
                    <span class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider">
                        Compliance
                    </span>
                </div>
            </div>
        </div>

        <!-- Metrics List -->
        <div class="space-y-2.5 text-xs">
            <div class="flex items-center justify-between border-b border-border/40 pb-2">
                <span class="text-muted-foreground">Respon Pertama Tepat Waktu</span>
                <span class="font-bold text-foreground">99.1%</span>
            </div>

            <div class="flex items-center justify-between border-b border-border/40 pb-2">
                <span class="text-muted-foreground">Resolusi Perbaikan Tepat Waktu</span>
                <span class="font-bold text-foreground">94.8%</span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-muted-foreground">Kepuasan Pelapor (CSAT)</span>
                <span class="font-bold text-foreground">4.8 / 5.0</span>
            </div>
        </div>
    </div>

    <!-- Target Footer -->
    <div class="mt-4 flex items-center justify-between rounded-lg bg-muted/40 px-3 py-2 text-xs">
        <span class="text-muted-foreground text-[11px]">Standar Target Kontrak</span>
        <span class="font-extrabold text-foreground">&ge; 95.0%</span>
    </div>
</div>
