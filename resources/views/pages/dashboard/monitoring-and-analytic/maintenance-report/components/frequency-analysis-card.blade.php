@props([
    'damageFrequency' => [
        'by_facility' => [],
        'by_category' => [],
    ],
])

@php
    $byFacility = $damageFrequency['by_facility'] ?? [];
    $byCategory = $damageFrequency['by_category'] ?? [];
@endphp

<x-ui.card class="p-5">
    <div class="border-b border-border pb-3">
        <h3 class="text-sm font-bold text-foreground sm:text-base">
            Frekuensi Kerusakan Fasilitas
        </h3>
        <p class="mt-0.5 text-xs text-muted-foreground">
            Distribusi frekuensi perbaikan per kategori dan aset paling sering bermasalah
        </p>
    </div>

    <div class="mt-4 space-y-5">
        <div>
            <h4 class="mb-2.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                Per Kategori Fasilitas
            </h4>
            @if (empty($byCategory))
                <p class="text-xs text-muted-foreground">Belum ada data kategori kerusakan.</p>
            @else
                <div class="space-y-3">
                    @foreach ($byCategory as $category)
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-foreground">{{ $category['category_name'] }}</span>
                                <span class="text-muted-foreground">
                                    {{ number_format((int) $category['repair_count']) }}x ({{ number_format((float) $category['percentage'], 1) }}%)
                                </span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-secondary">
                                <div
                                    class="h-full rounded-full bg-primary transition-all"
                                    style="width: {{ min(100, max(0, (float) $category['percentage'])) }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border-t border-border pt-4">
            <h4 class="mb-2.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                Aset Paling Sering Diperbaiki
            </h4>
            @if (empty($byFacility))
                <p class="text-xs text-muted-foreground">Belum ada riwayat perbaikan aset.</p>
            @else
                <div class="space-y-2">
                    @foreach (array_slice($byFacility, 0, 5) as $item)
                        <div class="flex items-center justify-between rounded-lg border border-border bg-background px-3 py-2 text-xs">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-foreground">{{ $item['facility_name'] }}</p>
                                <p class="truncate text-muted-foreground">{{ $item['building_name'] }} &middot; {{ $item['category_name'] }}</p>
                            </div>
                            <div class="ml-3 shrink-0 text-right">
                                <x-ui.badge variant="warning">{{ $item['repair_count'] }}x</x-ui.badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-ui.card>
