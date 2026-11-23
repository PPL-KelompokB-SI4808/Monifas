<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    @foreach ([
        [
            'label'   => 'Total Gedung',
            'value'   => \App\Models\Building::count(),
            'note'    => 'Terdaftar di sistem',
            'icon'    => 'building-2',
            'color'   => 'text-primary bg-primary/10',
        ],
        [
            'label'   => 'Gedung Aktif',
            'value'   => \App\Models\Building::where('is_active', true)->count(),
            'note'    => 'Siap digunakan',
            'icon'    => 'check-circle-2',
            'color'   => 'text-emerald-600 bg-emerald-500/10',
        ],
        [
            'label'   => 'Total Lantai',
            'value'   => \App\Models\Building::sum('total_floors'),
            'note'    => 'Akumulasi seluruh gedung',
            'icon'    => 'layers',
            'color'   => 'text-muted-foreground bg-secondary',
        ],
    ] as $card)
        <div class="rounded-xl border border-border bg-background p-5"
             style="transition: box-shadow 200ms, transform 200ms;"
             onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.08)';this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'">
            <div class="flex items-start justify-between">
                <span class="text-xs font-medium text-muted-foreground">{{ $card['label'] }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $card['color'] }}">
                    @switch($card['icon'])
                        @case('building-2') <x-lucide-building-2 class="h-4 w-4" /> @break
                        @case('check-circle-2') <x-lucide-check-circle-2 class="h-4 w-4" /> @break
                        @case('layers') <x-lucide-layers class="h-4 w-4" /> @break
                    @endswitch
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                {{ $card['value'] }}
            </p>
            <p class="mt-1 text-xs text-muted-foreground">{{ $card['note'] }}</p>
        </div>
    @endforeach
</div>
