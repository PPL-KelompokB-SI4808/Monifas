@props([
    'categories' => [
        [
            'name' => 'HVAC / Tata Udara (AC Central)',
            'incidents' => 48,
            'percentage' => 34,
            'color' => 'bg-blue-600',
            'dot' => 'bg-blue-600',
        ],
        [
            'name' => 'Kelistrikan & Penerangan',
            'incidents' => 36,
            'percentage' => 25,
            'color' => 'bg-amber-500',
            'dot' => 'bg-amber-500',
        ],
        [
            'name' => 'Plumbing & Pompa Air Bersih',
            'incidents' => 29,
            'percentage' => 20,
            'color' => 'bg-teal-500',
            'dot' => 'bg-teal-500',
        ],
        [
            'name' => 'Lift Penumpang & Eskalator',
            'incidents' => 18,
            'percentage' => 13,
            'color' => 'bg-rose-500',
            'dot' => 'bg-rose-500',
        ],
        [
            'name' => 'Sipil & Sanitasi',
            'incidents' => 11,
            'percentage' => 8,
            'color' => 'bg-slate-500',
            'dot' => 'bg-slate-500',
        ],
    ]
])

<div class="flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-xs">
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-foreground sm:text-sm">
                Frekuensi Kerusakan Fasilitas
            </h3>
            <span class="text-xs text-muted-foreground">Oktober 2024</span>
        </div>

        <p class="mt-3 text-xs text-muted-foreground leading-relaxed">
            Daftar kategori fasilitas dengan frekuensi breakdown &amp; corrective tertinggi:
        </p>

        <!-- Category Progress Bars -->
        <div class="mt-4 space-y-4">
            @foreach ($categories as $cat)
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $cat['dot'] }}"></span>
                            <span class="font-medium text-foreground">{{ $cat['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-foreground">{{ $cat['incidents'] }} insiden</span>
                            <span class="text-muted-foreground">({{ $cat['percentage'] }}%)</span>
                        </div>
                    </div>
                    <!-- Progress track -->
                    <div class="h-2 w-full overflow-hidden rounded-full bg-muted/60">
                        <div
                            class="h-full rounded-full {{ $cat['color'] }} transition-all duration-500"
                            style="width: {{ $cat['percentage'] }}%"
                        ></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Footer Note -->
    <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-t border-border/60 pt-3 text-[11px]">
        <span class="text-muted-foreground">
            Kompresor HVAC Lantai 4 membutuhkan overhaul berkala.
        </span>
        <a href="#" class="font-semibold text-primary hover:underline whitespace-nowrap">
            Detail Analisis &rarr;
        </a>
    </div>
</div>
