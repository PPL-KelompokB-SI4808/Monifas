@props([
    'records' => [
        [
            'id' => 'WO-2024-1089',
            'date' => '24 Okt 2024',
            'time' => '09:15 WIB',
            'facility' => 'AC Central Chiller #02',
            'location' => 'Gedung Utama - Lantai 4 (Sayap Barat)',
            'type' => 'Preventif Rutin',
            'type_style' => 'bg-blue-50 text-blue-700 border-blue-200',
            'technician' => 'Budi Santoso',
            'technician_role' => 'Tim HVAC Internal',
            'cost' => 'Rp 1.850.000',
            'sparepart' => 'Ganti Filter Udara (2 pcs)',
            'status' => 'Selesai',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'dot_style' => 'bg-emerald-500',
        ],
        [
            'id' => 'WO-2024-1088',
            'date' => '23 Okt 2024',
            'time' => '14:30 WIB',
            'facility' => 'Lift Penumpang Tower B (Lift 3)',
            'location' => 'Gedung Annex - Seluruh Lantai',
            'type' => 'Emergency / Breakdown',
            'type_style' => 'bg-rose-50 text-rose-700 border-rose-200',
            'technician' => 'PT Schindler Indonesia',
            'technician_role' => 'Vendor Eskalasi SLA-1',
            'cost' => 'Rp 12.400.000',
            'sparepart' => 'Sensor Pintu & Safety Switch',
            'status' => 'Selesai',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'dot_style' => 'bg-emerald-500',
        ],
        [
            'id' => 'WO-2024-1087',
            'date' => '23 Okt 2024',
            'time' => '08:00 WIB',
            'facility' => 'Panel Distribusi Listrik MDP',
            'location' => 'Basement 1 - Ruang Trafo Utama',
            'type' => 'Korektif',
            'type_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'technician' => 'Hendra Wijaya',
            'technician_role' => 'Teknisi Kelistrikan',
            'cost' => 'Rp 4.750.000',
            'sparepart' => 'Overload Relay & Kontaktor',
            'status' => 'Dalam Pengerjaan',
            'status_style' => 'bg-blue-50 text-blue-700 border-blue-200',
            'dot_style' => 'bg-blue-500',
        ],
        [
            'id' => 'WO-2024-1086',
            'date' => '22 Okt 2024',
            'time' => '11:10 WIB',
            'facility' => 'Booster Pump Air Bersih #01',
            'location' => 'Rooftop - Tower A',
            'type' => 'Korektif',
            'type_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'technician' => 'Ahmad Fauzi',
            'technician_role' => 'Tim Plumbing & Sanitasi',
            'cost' => 'Rp 2.100.000',
            'sparepart' => 'Mechanical Seal Pompa Grundfos',
            'status' => 'Tunggu Part',
            'status_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'dot_style' => 'bg-amber-500',
        ],
        [
            'id' => 'WO-2024-1085',
            'date' => '21 Okt 2024',
            'time' => '16:45 WIB',
            'facility' => 'Genset Emergency 500 kVA',
            'location' => 'Powerhouse Gedung Parkir',
            'type' => 'Preventif Rutin',
            'type_style' => 'bg-blue-50 text-blue-700 border-blue-200',
            'technician' => 'Dedi Mulyadi & Tim',
            'technician_role' => 'Teknisi Mekanikal',
            'cost' => 'Rp 8.900.000',
            'sparepart' => 'Ganti Oli Mesin & Filter Solar',
            'status' => 'Selesai',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'dot_style' => 'bg-emerald-500',
        ],
    ]
])

<div class="rounded-xl border border-border bg-card p-5 shadow-xs">
    <!-- Header with Filters -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-border/60 pb-4">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Riwayat Aktivitas Pemeliharaan & Perbaikan
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Log lengkap seluruh tiket maintenance yang tercatat pada sistem MONIFAS.
            </p>
        </div>

        <!-- Filter Selects -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Gedung -->
            <div class="relative">
                <select class="appearance-none rounded-lg border border-border bg-background py-1.5 pl-3 pr-8 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Gedung</option>
                    <option value="utama">Gedung Utama</option>
                    <option value="annex">Gedung Annex</option>
                    <option value="parkir">Gedung Parkir</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>

            <!-- Filter Tipe -->
            <div class="relative">
                <select class="appearance-none rounded-lg border border-border bg-background py-1.5 pl-3 pr-8 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Tipe</option>
                    <option value="preventif">Preventif Rutin</option>
                    <option value="korektif">Korektif</option>
                    <option value="emergency">Emergency / Breakdown</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>

            <!-- Filter Status -->
            <div class="relative">
                <select class="appearance-none rounded-lg border border-border bg-background py-1.5 pl-3 pr-8 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
                    <option value="">Semua Status</option>
                    <option value="selesai">Selesai</option>
                    <option value="pengerjaan">Dalam Pengerjaan</option>
                    <option value="tunggu">Tunggu Part</option>
                </select>
                <x-lucide-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    <th scope="col" class="pb-3 pr-4 font-semibold">ID Tiket</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Tanggal &amp; Waktu</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Fasilitas &amp; Lokasi</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Tipe Pemeliharaan</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Teknisi Bertugas</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Biaya / Sparepart</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Status</th>
                    <th scope="col" class="pb-3 pl-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach ($records as $item)
                    <tr class="transition hover:bg-muted/20">
                        <!-- ID Tiket -->
                        <td class="py-3.5 pr-4 font-mono font-bold text-primary">
                            <a href="#" class="hover:underline">
                                {{ $item['id'] }}
                            </a>
                        </td>

                        <!-- Tanggal & Waktu -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-semibold text-foreground">{{ $item['date'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['time'] }}</div>
                        </td>

                        <!-- Fasilitas & Lokasi -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-foreground">{{ $item['facility'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['location'] }}</div>
                        </td>

                        <!-- Tipe Pemeliharaan -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-[10px] font-semibold {{ $item['type_style'] }}">
                                {{ $item['type'] }}
                            </span>
                        </td>

                        <!-- Teknisi Bertugas -->
                        <td class="py-3.5 px-4">
                            <div class="font-medium text-foreground">{{ $item['technician'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['technician_role'] }}</div>
                        </td>

                        <!-- Biaya / Sparepart -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-foreground">{{ $item['cost'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['sparepart'] }}</div>
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $item['status_style'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $item['dot_style'] }}"></span>
                                {{ $item['status'] }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 pl-4 text-right whitespace-nowrap">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-md border border-border bg-background px-3 py-1 text-xs font-semibold text-foreground transition hover:bg-secondary cursor-pointer"
                            >
                                Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-border/60 pt-4 text-xs text-muted-foreground">
        <div>
            Menampilkan <span class="font-semibold text-foreground">1 - 5</span> dari <span class="font-semibold text-foreground">142</span> total laporan aktivitas
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center gap-1">
            <button type="button" class="rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary cursor-pointer disabled:opacity-50">
                &larr; Prev
            </button>
            <button type="button" class="rounded-md bg-primary px-2.5 py-1 text-xs font-bold text-primary-foreground shadow-2xs">
                1
            </button>
            <button type="button" class="rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-foreground hover:bg-secondary cursor-pointer">
                2
            </button>
            <button type="button" class="rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-foreground hover:bg-secondary cursor-pointer">
                3
            </button>
            <span class="px-1 text-muted-foreground">...</span>
            <button type="button" class="rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-foreground hover:bg-secondary cursor-pointer">
                29
            </button>
            <button type="button" class="rounded-md border border-border bg-background px-2.5 py-1 text-xs font-medium text-muted-foreground hover:bg-secondary cursor-pointer">
                Next &rarr;
            </button>
        </div>
    </div>
</div>
