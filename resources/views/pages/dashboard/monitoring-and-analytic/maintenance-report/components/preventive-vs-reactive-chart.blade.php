@props([
    'preventiveVsReactive' => [
        'total' => 0,
        'preventive' => ['count' => 0, 'percentage' => 0.0, 'total_cost' => 0.0],
        'reactive' => ['count' => 0, 'percentage' => 0.0, 'total_cost' => 0.0],
    ],
])

@php
    $preventive = $preventiveVsReactive['preventive'] ?? ['count' => 0, 'percentage' => 0.0, 'total_cost' => 0.0];
    $reactive = $preventiveVsReactive['reactive'] ?? ['count' => 0, 'percentage' => 0.0, 'total_cost' => 0.0];
@endphp

<x-ui.card
    class="flex flex-col justify-between p-5"
    x-data="monitoringAndAnalyticMaintenanceReport(@js($preventiveVsReactive))"
>
    <div class="border-b border-border pb-3">
        <h3 class="text-sm font-bold text-foreground sm:text-base">
            Preventive vs Reaktif / Darurat
        </h3>
        <p class="mt-0.5 text-xs text-muted-foreground">
            Perbandingan rasio pekerjaan terjadwal dan perbaikan insidental
        </p>
    </div>

    <div class="relative mx-auto my-5 h-52 w-52">
        <canvas x-ref="canvas" aria-label="Grafik perbandingan preventive dan reaktif"></canvas>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-lg border border-border bg-background p-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                <span class="font-semibold text-foreground">Preventive Maintenance</span>
            </div>
            <p class="mt-1.5 text-lg font-bold text-foreground">
                {{ number_format((int) $preventive['count']) }}
                <span class="text-xs font-normal text-muted-foreground">({{ number_format((float) $preventive['percentage'], 1) }}%)</span>
            </p>
            <p class="mt-0.5 text-muted-foreground">
                Biaya: Rp {{ number_format((float) $preventive['total_cost'], 0, ',', '.') }}
            </p>
        </div>

        <div class="rounded-lg border border-border bg-background p-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-warning"></span>
                <span class="font-semibold text-foreground">Reaktif / Darurat</span>
            </div>
            <p class="mt-1.5 text-lg font-bold text-foreground">
                {{ number_format((int) $reactive['count']) }}
                <span class="text-xs font-normal text-muted-foreground">({{ number_format((float) $reactive['percentage'], 1) }}%)</span>
            </p>
            <p class="mt-0.5 text-muted-foreground">
                Biaya: Rp {{ number_format((float) $reactive['total_cost'], 0, ',', '.') }}
            </p>
        </div>
    </div>
</x-ui.card>
