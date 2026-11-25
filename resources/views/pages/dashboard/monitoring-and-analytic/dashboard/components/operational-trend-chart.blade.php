@props([
    'trends' => [
        'labels' => [],
        'datasets' => [
            'incoming' => [],
            'completed' => [],
        ],
    ],
])

<x-ui.card
    class="flex flex-col justify-between p-5"
    x-data="monitoringAndAnalyticDashboard(@js($trends))"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Tren Volume Laporan Periodik
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Perbandingan laporan masuk dan penyelesaian pekerjaan per periode
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
            <span class="inline-flex items-center gap-1.5">
                <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                Laporan Masuk
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="h-2.5 w-2.5 rounded-full bg-success"></span>
                Selesai Dikerjakan
            </span>
        </div>
    </div>

    <div class="relative mt-5 h-72 w-full">
        <canvas x-ref="canvas" aria-label="Grafik tren volume laporan periodik"></canvas>
    </div>
</x-ui.card>
