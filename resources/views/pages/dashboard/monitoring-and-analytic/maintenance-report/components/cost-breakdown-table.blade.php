@props([
    'costAnalysis' => [
        'total_cost' => 0,
        'total_material_items' => 0,
        'total_quantity' => 0,
        'by_material_type' => [],
        'top_materials' => [],
    ],
])

@php
    $byType = $costAnalysis['by_material_type'] ?? [];
    $topMaterials = $costAnalysis['top_materials'] ?? [];
@endphp

<x-ui.card class="p-5">
    <div class="flex flex-col gap-3 border-b border-border pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Analisis Biaya Perbaikan &amp; Konsumsi Material
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Rincian pengeluaran suku cadang dan jenis material yang paling banyak digunakan
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge variant="primary">
                Total: Rp {{ number_format((float) ($costAnalysis['total_cost'] ?? 0), 0, ',', '.') }}
            </x-ui.badge>
            <x-ui.badge variant="default">
                {{ number_format((int) ($costAnalysis['total_material_items'] ?? 0)) }} Item Material
            </x-ui.badge>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="space-y-2 lg:col-span-4">
            <h4 class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                Per Jenis Material
            </h4>
            @if (empty($byType))
                <p class="text-xs text-muted-foreground">Belum ada data konsumsi material.</p>
            @else
                <div class="space-y-2">
                    @foreach ($byType as $type)
                        <div class="flex items-center justify-between rounded-lg border border-border bg-background px-3 py-2.5 text-xs">
                            <div>
                                <p class="font-medium text-foreground">{{ $type['material_type'] }}</p>
                                <p class="text-muted-foreground">{{ $type['item_count'] }} transaksi &middot; Qty {{ number_format((float) $type['total_quantity'], 1) }}</p>
                            </div>
                            <span class="font-semibold text-foreground">
                                Rp {{ number_format((float) $type['total_cost'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="lg:col-span-8">
            <h4 class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                Material &amp; Suku Cadang Teratas
            </h4>
            @if (empty($topMaterials))
                <div class="flex flex-col items-center justify-center rounded-lg border border-border bg-background py-8 text-center">
                    <x-lucide-package class="h-7 w-7 text-muted-foreground" />
                    <p class="mt-2 text-xs text-muted-foreground">Belum ada pemakaian material pada periode ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-border text-muted-foreground">
                                <th class="py-2 pr-3 font-semibold">Nama Material</th>
                                <th class="px-3 py-2 font-semibold">Jenis</th>
                                <th class="px-3 py-2 text-right font-semibold">Kuantitas</th>
                                <th class="py-2 pl-3 text-right font-semibold">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($topMaterials as $material)
                                <tr class="hover:bg-secondary/50">
                                    <td class="py-2.5 pr-3 font-medium text-foreground">{{ $material['material_name'] }}</td>
                                    <td class="px-3 py-2.5 text-muted-foreground">{{ $material['material_type'] }}</td>
                                    <td class="px-3 py-2.5 text-right text-foreground">
                                        {{ number_format((float) $material['total_quantity'], 1) }} {{ $material['unit'] }}
                                    </td>
                                    <td class="py-2.5 pl-3 text-right font-semibold text-foreground">
                                        Rp {{ number_format((float) $material['total_cost'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-ui.card>
