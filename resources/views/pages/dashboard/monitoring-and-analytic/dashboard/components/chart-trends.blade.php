<div
    class="flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-xs"
    x-data="{
        chart: null,
        init() {
            this.$nextTick(() => {
                const ctx = this.$refs.canvas.getContext('2d');
                this.chart = new window.Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                        datasets: [
                            {
                                label: 'Laporan Masuk',
                                data: [95, 128, 88, 148, 108, 155],
                                backgroundColor: [
                                    'rgba(147, 197, 253, 0.85)',
                                    'rgba(147, 197, 253, 0.85)',
                                    'rgba(147, 197, 253, 0.85)',
                                    'rgba(147, 197, 253, 0.85)',
                                    'rgba(147, 197, 253, 0.85)',
                                    '#2563eb'
                                ],
                                borderRadius: 4,
                                borderSkipped: false,
                                barPercentage: 0.55,
                                categoryPercentage: 0.55,
                            },
                            {
                                label: 'Selesai Dikerjakan',
                                data: [90, 120, 80, 132, 102, 148],
                                backgroundColor: '#10b981',
                                borderRadius: 4,
                                borderSkipped: false,
                                barPercentage: 0.55,
                                categoryPercentage: 0.55,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: (ctx) => ctx.tick.label === 'Okt' ? '#2563eb' : '#64748b',
                                    font: (ctx) => ({
                                        weight: ctx.tick.label === 'Okt' ? 'bold' : 'normal',
                                        size: 11
                                    })
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: 200,
                                ticks: {
                                    stepSize: 50,
                                    color: '#94a3b8',
                                    font: { size: 10 }
                                },
                                grid: {
                                    color: '#f1f5f9',
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            });
        }
    }"
>
    <!-- Card Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Tren Laporan Masuk & Penyelesaian Pekerjaan
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Komparasi volume kendala terhadap tingkat penyelesaian teknis
            </p>
        </div>

        <!-- Custom Legend -->
        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5">
                <span class="h-3 w-3 rounded-xs bg-blue-300"></span>
                <span class="text-muted-foreground text-[11px]">Laporan Masuk</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="h-3 w-3 rounded-xs bg-emerald-500"></span>
                <span class="text-muted-foreground text-[11px]">Selesai Dikerjakan</span>
            </div>
        </div>
    </div>

    <!-- Chart Canvas Container -->
    <div class="relative mt-4 h-64 w-full">
        <canvas x-ref="canvas"></canvas>
    </div>

    <!-- Card Footer -->
    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-t border-border/60 pt-3 text-xs">
        <span class="text-muted-foreground text-[11px]">
            Rata-rata 148 keluhan terselesaikan / bulan
        </span>
        <a href="#" class="inline-flex items-center gap-1 font-semibold text-primary hover:underline text-[11px]">
            Lihat Analisis Detail
            <x-lucide-chevron-right class="h-3.5 w-3.5" />
        </a>
    </div>
</div>
