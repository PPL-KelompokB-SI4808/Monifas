@props([
    'technicianPerformance' => [
        'summary' => [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'completion_rate' => 0.0,
            'avg_resolution_hours' => 0.0,
        ],
        'technicians' => [],
    ],
])

@php
    $summary = $technicianPerformance['summary'] ?? [];
    $technicians = $technicianPerformance['technicians'] ?? [];
@endphp

<x-ui.card class="p-5">
    <div class="flex flex-col gap-3 border-b border-border pb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Performa Penyelesaian Tugas Tim Teknisi
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Ringkasan produktivitas, tingkat penyelesaian, dan rata-rata waktu penanganan
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge variant="primary">
                Total Tugas: {{ number_format((int) ($summary['total_tasks'] ?? 0)) }}
            </x-ui.badge>
            <x-ui.badge variant="success">
                Penyelesaian: {{ number_format((float) ($summary['completion_rate'] ?? 0), 1) }}%
            </x-ui.badge>
            <x-ui.badge variant="default">
                Rata-rata: {{ number_format((float) ($summary['avg_resolution_hours'] ?? 0), 1) }} jam
            </x-ui.badge>
        </div>
    </div>

    @if (empty($technicians))
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <x-lucide-users class="h-8 w-8 text-muted-foreground" />
            <p class="mt-2 text-sm font-medium text-foreground">Belum ada data penugasan teknisi</p>
            <p class="mt-1 text-xs text-muted-foreground">
                Data performa akan muncul ketika penugasan teknisi tersedia pada filter yang dipilih.
            </p>
        </div>
    @else
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border text-muted-foreground">
                        <th class="py-2.5 pr-4 font-semibold">Teknisi</th>
                        <th class="px-3 py-2.5 font-semibold">Kode</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Total Tugas</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Selesai</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Aktif</th>
                        <th class="px-3 py-2.5 text-right font-semibold">Tingkat Selesai</th>
                        <th class="py-2.5 pl-3 text-right font-semibold">Rata-rata Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($technicians as $technician)
                        <tr class="transition-colors hover:bg-secondary/50">
                            <td class="py-3 pr-4 font-medium text-foreground">
                                {{ $technician['officer_name'] }}
                            </td>
                            <td class="px-3 py-3 font-mono text-muted-foreground">
                                {{ $technician['employee_code'] }}
                            </td>
                            <td class="px-3 py-3 text-right text-foreground">
                                {{ number_format((int) $technician['total_tasks']) }}
                            </td>
                            <td class="px-3 py-3 text-right font-medium text-success">
                                {{ number_format((int) $technician['completed_tasks']) }}
                            </td>
                            <td class="px-3 py-3 text-right text-warning">
                                {{ number_format((int) $technician['active_tasks']) }}
                            </td>
                            <td class="px-3 py-3 text-right">
                                <x-ui.badge :variant="$technician['completion_rate'] >= 80 ? 'success' : ($technician['completion_rate'] >= 50 ? 'warning' : 'default')">
                                    {{ number_format((float) $technician['completion_rate'], 1) }}%
                                </x-ui.badge>
                            </td>
                            <td class="py-3 pl-3 text-right text-muted-foreground">
                                {{ number_format((float) $technician['avg_resolution_hours'], 1) }} jam
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-ui.card>
