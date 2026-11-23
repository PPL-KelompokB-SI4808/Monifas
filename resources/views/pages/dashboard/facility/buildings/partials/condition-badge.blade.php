@props(['condition'])

@php
    $map = [
        'good'         => ['bg-emerald-500/10 text-emerald-600 border-emerald-500/20', 'Baik'],
        'minor_damage' => ['bg-yellow-500/10 text-yellow-700 border-yellow-500/20',   'Rusak Ringan'],
        'major_damage' => ['bg-orange-500/10 text-orange-700 border-orange-500/20',   'Rusak Berat'],
        'out_of_order' => ['bg-red-500/10 text-red-600 border-red-500/20',            'Tidak Berfungsi'],
    ];
    $key    = $condition->value ?? $condition;
    $style  = $map[$key][0] ?? 'bg-secondary text-muted-foreground border-border';
    $label  = $map[$key][1] ?? 'Tidak Diketahui';
@endphp

<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $style }}">
    {{ $label }}
</span>
