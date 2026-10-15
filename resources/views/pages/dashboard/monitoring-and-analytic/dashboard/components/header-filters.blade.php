@props([
    'periods' => [
        'today' => 'Hari Ini',
        'week' => 'Minggu Ini',
        'month' => 'Bulan Ini',
        'year' => 'Tahun Ini',
    ],
    'activePeriod' => 'month',
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" x-data="{ currentPeriod: '{{ $activePeriod }}' }">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
            Ringkasan Kondisi Operasional
        </h1>
        <p class="mt-1 text-xs text-muted-foreground sm:text-sm">
            Pemantauan kesehatan infrastruktur dan kinerja perawatan fasilitas secara real-time.
        </p>
    </div>

    <!-- Time Period Filter Tabs -->
    <div class="inline-flex items-center rounded-lg border border-border bg-background p-1 shadow-2xs">
        @foreach ($periods as $key => $label)
            <button
                type="button"
                @click="currentPeriod = '{{ $key }}'"
                :class="currentPeriod === '{{ $key }}'
                    ? 'bg-primary text-primary-foreground font-semibold shadow-xs'
                    : 'text-muted-foreground hover:text-foreground font-medium'"
                class="rounded-md px-3 py-1.5 text-xs transition-all duration-150 cursor-pointer"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>
