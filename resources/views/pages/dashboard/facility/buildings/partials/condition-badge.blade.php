@props(['condition'])

@php
    $class = match($condition->value ?? $condition) {
        'good' => 'bg-green-100 text-green-800',
        'minor_damage' => 'bg-yellow-100 text-yellow-800',
        'major_damage' => 'bg-orange-100 text-orange-800',
        'out_of_order' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
    
    $label = match($condition->value ?? $condition) {
        'good' => 'Baik',
        'minor_damage' => 'Rusak Ringan',
        'major_damage' => 'Rusak Berat',
        'out_of_order' => 'Tidak Berfungsi',
        default => 'Unknown',
    };
@endphp

<span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
    {{ $label }}
</span>