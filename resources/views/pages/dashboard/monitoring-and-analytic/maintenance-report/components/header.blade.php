@props([
    'periods' => [
        'month' => 'Bulan Ini',
        'quarter' => 'Kuartal Ini',
        'year' => '2024',
    ],
    'activePeriod' => 'month',
])

<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between" x-data="{ currentPeriod: '{{ $activePeriod }}' }">
    <div>
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-primary"></span>
            <span class="text-xs font-bold tracking-wider text-primary uppercase">Monitoring & Analytic</span>
        </div>
        <h1 class="mt-1 text-xl font-bold tracking-tight text-foreground sm:text-2xl">
            Laporan & Analisis Pemeliharaan
        </h1>
        <p class="mt-1 text-xs text-muted-foreground sm:text-sm">
            Analisis histori pemeliharaan preventif, korektif, dan efisiensi biaya fasilitas gedung.
        </p>
    </div>

    <!-- Actions & Filter Controls -->
    <div class="flex flex-wrap items-center gap-3">
        <!-- Period Tabs -->
        <div class="inline-flex items-center rounded-lg border border-border bg-background p-1 shadow-2xs">
            @foreach ($periods as $key => $label)
                <button
                    type="button"
                    @click="currentPeriod = '{{ $key }}'"
                    :class="currentPeriod === '{{ $key }}'
                        ? 'bg-secondary text-foreground font-semibold shadow-xs'
                        : 'text-muted-foreground hover:text-foreground font-medium'"
                    class="rounded-md px-3 py-1.5 text-xs transition cursor-pointer"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Export PDF Button -->
        <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50/60 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 cursor-pointer"
        >
            <x-lucide-file-text class="h-4 w-4" />
            Ekspor PDF
        </button>

        <!-- Export Excel Button -->
        <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs transition hover:bg-emerald-700 cursor-pointer"
        >
            <x-lucide-download class="h-4 w-4" />
            Ekspor Excel (.xlsx)
        </button>
    </div>
</div>
