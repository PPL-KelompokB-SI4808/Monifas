<div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
    <div>
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-border/60 pb-3">
            <div>
                <h3 class="text-sm font-bold text-foreground sm:text-base">
                    Beban Kerja Aktif Teknisi
                </h3>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Distribusi alokasi penanganan untuk mencegah overload & breach SLA
                </p>
            </div>
            <span class="inline-flex items-center rounded border border-border bg-muted/30 px-2 py-0.5 text-[10px] font-medium text-muted-foreground">
                Hari Ini
            </span>
        </div>

        <!-- Workload Bars -->
        <div class="mt-4 space-y-3.5">
            <!-- Budi Santoso -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-foreground">Budi Santoso</span>
                    <span class="text-muted-foreground font-medium">3 Aktif (Kapasitas 75%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div class="h-full rounded-full bg-blue-600 transition-all duration-500" style="width: 75%"></div>
                </div>
            </div>

            <!-- Dimas Pratama -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-foreground">Dimas Pratama</span>
                    <span class="text-muted-foreground font-medium">2 Aktif (Kapasitas 50%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div class="h-full rounded-full bg-teal-500 transition-all duration-500" style="width: 50%"></div>
                </div>
            </div>

            <!-- Rian Hidayat -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-foreground">Rian Hidayat</span>
                    <span class="text-amber-600 font-bold">4 Aktif (Kapasitas 95%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div class="h-full rounded-full bg-amber-500 transition-all duration-500" style="width: 95%"></div>
                </div>
            </div>

            <!-- Ahmad Fauzi -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-foreground">Ahmad Fauzi</span>
                    <span class="text-muted-foreground font-medium">1 Aktif (Kapasitas 25%)</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Status Box -->
    <div class="mt-5 flex items-center justify-between rounded-xl border border-border/80 bg-muted/20 p-3">
        <div>
            <div class="text-xs font-bold text-foreground">Status Tim Lapangan</div>
            <div class="text-[10px] text-muted-foreground font-medium">
                14 On-Duty &bull; 3 Siaga &bull; 1 Izin
            </div>
        </div>
        <button
            type="button"
            class="rounded-md border border-border bg-background px-3 py-1.5 text-xs font-semibold text-foreground transition hover:bg-secondary cursor-pointer"
        >
            Atur Shift
        </button>
    </div>
</div>
