@props([
    'issues' => [
        [
            'name' => 'Chiller Central Unit #02',
            'code' => 'ID: FAS-HVAC-089',
            'location' => 'Rooftop Gedung Utama A',
            'incidents' => 14,
            'period' => '30 hari',
            'urgency' => 'Kritis',
            'urgency_style' => 'bg-rose-50 text-rose-600 border-rose-200',
        ],
        [
            'name' => 'Lift Penumpang Passenger #04',
            'code' => 'ID: FAS-ELEV-012',
            'location' => 'Tower Logistik B (Zona Barat)',
            'incidents' => 9,
            'period' => '30 hari',
            'urgency' => 'Tinggi',
            'urgency_style' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
        [
            'name' => 'Genset Backup Utama 500kVA',
            'code' => 'ID: FAS-PWR-003',
            'location' => 'Ruang Powerhouse Basement',
            'incidents' => 7,
            'period' => '30 hari',
            'urgency' => 'Tinggi',
            'urgency_style' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
        [
            'name' => 'Pompa Distribusi Booster Pompa #1',
            'code' => 'ID: FAS-PLM-044',
            'location' => 'Ruang Utilitas Lt. 1',
            'incidents' => 6,
            'period' => '30 hari',
            'urgency' => 'Sedang',
            'urgency_style' => 'bg-blue-50 text-blue-700 border-blue-200',
        ],
        [
            'name' => 'Automatic Sliding Gate Barrier Utama',
            'code' => 'ID: FAS-ACC-001',
            'location' => 'Gerbang Akses Masuk Timur',
            'incidents' => 5,
            'period' => '30 hari',
            'urgency' => 'Rendah',
            'urgency_style' => 'bg-slate-100 text-slate-700 border-slate-200',
        ],
    ]
])

<div class="rounded-xl border border-border bg-card p-5 shadow-xs">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Fasilitas Paling Sering Mengalami Kendala
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Identifikasi unit memerlukan audit atau preventive maintenance berkala
            </p>
        </div>
        <a href="#" class="text-xs font-semibold text-primary hover:underline self-start sm:self-auto">
            Ekspor Data
        </a>
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    <th scope="col" class="pb-3 pr-4 font-semibold">Nama Fasilitas & Kode Unit</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Lokasi / Gedung</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Frekuensi Kendala</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Tingkat Urgensi</th>
                    <th scope="col" class="pb-3 pl-4 text-right font-semibold">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach ($issues as $item)
                    <tr class="transition hover:bg-muted/30">
                        <td class="py-3 pr-4">
                            <div class="font-bold text-foreground">{{ $item['name'] }}</div>
                            <div class="text-[10px] font-mono text-muted-foreground">{{ $item['code'] }}</div>
                        </td>
                        <td class="py-3 px-4 text-muted-foreground">
                            {{ $item['location'] }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-foreground">{{ $item['incidents'] }} Insiden</span>
                            <span class="text-muted-foreground">/ {{ $item['period'] }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-[10px] font-bold {{ $item['urgency_style'] }}">
                                {{ $item['urgency'] }}
                            </span>
                        </td>
                        <td class="py-3 pl-4 text-right">
                            <a href="#" class="font-semibold text-primary hover:underline">
                                Audit Unit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
