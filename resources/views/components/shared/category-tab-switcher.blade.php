@props([
    'active' => 'facility-categories',
])

@php
    $tabs = [
        [
            'id' => 'locations',
            'label' => 'Master Lokasi Kawasan',
            'route' => 'location-and-category.locations.index',
            'icon' => 'lucide-map-pin',
        ],
        [
            'id' => 'facility-categories',
            'label' => 'Kategori Fasilitas',
            'route' => 'location-and-category.facility-categories.index',
            'icon' => 'lucide-boxes',
        ],
        [
            'id' => 'damage-categories',
            'label' => 'Kategori Kerusakan',
            'route' => 'location-and-category.damage-categories.index',
            'icon' => 'lucide-alert-triangle',
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5 p-1 bg-muted/60 border border-border rounded-xl w-fit text-xs font-medium']) }}>
    @foreach ($tabs as $tab)
        @php
            $isActive = $active === $tab['id'];
        @endphp
        <a
            href="{{ route($tab['route']) }}"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all {{ $isActive ? 'bg-background text-foreground shadow-2xs font-semibold border border-border/60' : 'text-muted-foreground hover:text-foreground hover:bg-background/40' }}"
        >
            <x-dynamic-component :component="$tab['icon']" class="h-3.5 w-3.5 shrink-0 {{ $isActive ? 'text-primary' : 'text-muted-foreground' }}" />
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</div>
