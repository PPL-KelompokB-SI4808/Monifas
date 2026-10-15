@props([
    'activities' => [
        [
            'code' => 'TK-2023-1092',
            'title' => 'Tekanan Air Drop di Lantai 5 Wing Kiri',
            'location' => 'Gedung Utama Lt. 5',
            'category' => 'Plumbing',
            'tech_avatar' => 'BP',
            'technician' => 'Budi Prasetyo',
            'status' => 'Dalam Proses',
            'status_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'dot_style' => 'bg-amber-500',
            'time' => '18 menit yang lalu',
        ],
        [
            'code' => 'TK-2023-1091',
            'title' => 'Fluktuasi Tegangan Panel Distribusi Utama',
            'location' => 'Substation B3',
            'category' => 'Elektrikal',
            'tech_avatar' => 'RA',
            'technician' => 'Rian Anggara',
            'status' => 'Investigasi',
            'status_style' => 'bg-blue-50 text-blue-700 border-blue-200',
            'dot_style' => 'bg-blue-500',
            'time' => '45 menit yang lalu',
        ],
        [
            'code' => 'TK-2023-1088',
            'title' => 'Penggantian Motor Fan Cooling Tower #3',
            'location' => 'Rooftop Area A',
            'category' => 'HVAC',
            'tech_avatar' => 'DK',
            'technician' => 'Dedi Kurnia',
            'status' => 'Menunggu Sparepart',
            'status_style' => 'bg-rose-50 text-rose-700 border-rose-200',
            'dot_style' => 'bg-rose-500',
            'time' => '2 jam yang lalu',
        ],
        [
            'code' => 'TK-2023-1085',
            'title' => 'Kalibrasi Sensor Sensor Suhu Server Room',
            'location' => 'Data Center Lt. 2',
            'category' => 'Sistem Otomasi',
            'tech_avatar' => 'WH',
            'technician' => 'Wahyu Hidayat',
            'status' => 'Selesai / Review',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'dot_style' => 'bg-emerald-500',
            'time' => '4 jam yang lalu',
        ],
    ]
])

<div class="rounded-xl border border-border bg-card p-5 shadow-xs">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Aktivitas & Laporan Penanganan Terkini
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Pembaruan langsung progres operasional teknisi di lapangan
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-semibold text-foreground transition hover:bg-secondary cursor-pointer"
            >
                <x-lucide-rotate-cw class="h-3.5 w-3.5 text-muted-foreground" />
                Refresh
            </button>
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-semibold text-primary-foreground shadow-xs transition hover:bg-primary/90 cursor-pointer"
            >
                <x-lucide-plus class="h-3.5 w-3.5" />
                Buat Laporan Baru
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    <th scope="col" class="pb-3 pr-4 font-semibold">Kode Tiket</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Deskripsi Gangguan</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Kategori</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Teknisi Bertugas</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Status Terkini</th>
                    <th scope="col" class="pb-3 px-4 font-semibold">Waktu Lapor</th>
                    <th scope="col" class="pb-3 pl-4 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach ($activities as $item)
                    <tr class="transition hover:bg-muted/30">
                        <!-- Kode Tiket -->
                        <td class="py-3 pr-4 font-mono font-bold text-primary">
                            <a href="#" class="hover:underline">
                                {{ $item['code'] }}
                            </a>
                        </td>

                        <!-- Deskripsi Gangguan -->
                        <td class="py-3 px-4">
                            <div class="font-bold text-foreground">{{ $item['title'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['location'] }}</div>
                        </td>

                        <!-- Kategori -->
                        <td class="py-3 px-4 text-muted-foreground">
                            {{ $item['category'] }}
                        </td>

                        <!-- Teknisi Bertugas -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-[10px] font-bold text-slate-700">
                                    {{ $item['tech_avatar'] }}
                                </span>
                                <span class="font-medium text-foreground">{{ $item['technician'] }}</span>
                            </div>
                        </td>

                        <!-- Status Terkini -->
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[11px] font-medium {{ $item['status_style'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $item['dot_style'] }}"></span>
                                {{ $item['status'] }}
                            </span>
                        </td>

                        <!-- Waktu Lapor -->
                        <td class="py-3 px-4 text-muted-foreground">
                            {{ $item['time'] }}
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 pl-4 text-right">
                            <button
                                type="button"
                                class="rounded p-1 text-muted-foreground hover:bg-secondary hover:text-foreground transition cursor-pointer"
                                title="Opsi"
                            >
                                <x-lucide-more-vertical class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
