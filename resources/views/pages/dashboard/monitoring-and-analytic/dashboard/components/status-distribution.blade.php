<div
    class="flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-xs"
    x-data="{
        chart: null,
        init() {
            this.$nextTick(() => {
                const ctx = this.$refs.canvas.getContext('2d');
                this.chart = new window.Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Laporan Baru (Belum Ditugaskan)',
                            'Dalam Pengerjaan Tim',
                            'Menunggu Suku Cadang',
                            'Verifikasi & Siap Ditutup'
                        ],
                        datasets: [{
                            data: [32, 58, 19, 19],
                            backgroundColor: [
                                '#3b82f6',
                                '#f59e0b',
                                '#ef4444',
                                '#10b981'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 11, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 8,
                                cornerRadius: 6,
                            }
                        }
                    }
                });
            });
        }
    }"
>
    <!-- Header -->
    <div class="flex items-start justify-between">
        <h3 class="text-sm font-bold text-foreground sm:text-base">
            Distribusi Status Laporan
        </h3>
        <span class="text-[11px] text-muted-foreground text-right leading-tight">
            Total: 128 Tiket<br>Aktif
        </span>
    </div>

    <!-- Donut Chart with Center Text -->
    <div class="relative my-4 flex h-44 w-full items-center justify-center">
        <canvas x-ref="canvas"></canvas>
        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
            <span class="text-[9px] font-bold uppercase tracking-wider text-muted-foreground">Status</span>
            <span class="text-xs font-black text-foreground">100%</span>
        </div>
    </div>

    <!-- Status Legend Breakdown -->
    <div class="space-y-2 border-t border-border/60 pt-3 text-xs">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"></span>
                <span class="text-muted-foreground text-[11px]">Laporan Baru (Belum Ditugaskan)</span>
            </div>
            <span class="font-bold text-foreground text-[11px]">32</span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500"></span>
                <span class="text-muted-foreground text-[11px]">Dalam Pengerjaan Tim</span>
            </div>
            <span class="font-bold text-foreground text-[11px]">58</span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-rose-500"></span>
                <span class="text-muted-foreground text-[11px]">Menunggu Suku Cadang</span>
            </div>
            <span class="font-bold text-foreground text-[11px]">19</span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500"></span>
                <span class="text-muted-foreground text-[11px]">Verifikasi & Siap Ditutup</span>
            </div>
            <span class="font-bold text-foreground text-[11px]">19</span>
        </div>
    </div>
</div>
