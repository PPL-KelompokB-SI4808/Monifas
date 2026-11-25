@props([
    'kpi' => [
        'active_reports' => 0,
        'completed_reports' => 0,
        'problematic_assets' => 0,
        'maintenance_tasks' => 0,
    ],
])

@php
    $cards = [
        [
            'label' => 'Total Laporan Aktif',
            'value' => number_format((int) ($kpi['active_reports'] ?? 0)),
            'subtitle' => 'Menunggu verifikasi hingga pengerjaan',
            'icon' => 'file-text',
            'badgeVariant' => 'primary',
            'badgeText' => 'Aktif',
        ],
        [
            'label' => 'Laporan Selesai',
            'value' => number_format((int) ($kpi['completed_reports'] ?? 0)),
            'subtitle' => 'Laporan yang telah dituntaskan',
            'icon' => 'check-circle-2',
            'badgeVariant' => 'success',
            'badgeText' => 'Selesai',
        ],
        [
            'label' => 'Aset Bermasalah',
            'value' => number_format((int) ($kpi['problematic_assets'] ?? 0)),
            'subtitle' => 'Fasilitas di luar kondisi baik',
            'icon' => 'alert-triangle',
            'badgeVariant' => 'error',
            'badgeText' => 'Perhatian',
        ],
        [
            'label' => 'Tugas Pemeliharaan',
            'value' => number_format((int) ($kpi['maintenance_tasks'] ?? 0)),
            'subtitle' => 'Penugasan aktif tim teknisi',
            'icon' => 'wrench',
            'badgeVariant' => 'warning',
            'badgeText' => 'Berjalan',
        ],
    ];
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($cards as $card)
        <x-ui.card :hover="true" class="p-5">
            <div class="flex items-start justify-between gap-3">
                <span class="text-xs font-medium text-muted-foreground">{{ $card['label'] }}</span>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-border bg-secondary text-primary">
                    <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-4 w-4" />
                </div>
            </div>
            <div class="mt-3 text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                {{ $card['value'] }}
            </div>
            <div class="mt-4 flex items-center justify-between gap-2">
                <span class="text-xs text-muted-foreground">{{ $card['subtitle'] }}</span>
                <x-ui.badge :variant="$card['badgeVariant']">{{ $card['badgeText'] }}</x-ui.badge>
            </div>
        </x-ui.card>
    @endforeach
</div>
