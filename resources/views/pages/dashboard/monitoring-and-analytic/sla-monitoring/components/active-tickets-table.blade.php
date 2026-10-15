@props([
    'tickets' => [
        [
            'code' => '#TK-8941',
            'facility' => 'AC Precision Server Down (Overheat)',
            'category' => 'HVAC Cooling Unit',
            'location' => 'Gedung Pusat, Lt. 2 (Data Center)',
            'priority' => 'Critical',
            'priority_style' => 'bg-rose-50 text-rose-700 border-rose-200',
            'technician' => 'Budi Santoso',
            'target_time' => 'Hari Ini, 14:30 WIB (2 Jam)',
            'countdown' => 'Sisa 18 Menit',
            'countdown_type' => 'warning',
            'status' => 'Mendekati Batas',
            'status_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'action_label' => 'Eskalasi',
            'action_type' => 'link-primary',
        ],
        [
            'code' => '#TK-8944',
            'facility' => 'Lift Penumpang 2 Macet di Lt. 4',
            'category' => 'Vertical Transport',
            'location' => 'Tower B, Lobby Barat',
            'priority' => 'Critical',
            'priority_style' => 'bg-rose-50 text-rose-700 border-rose-200',
            'technician' => 'Rian Hidayat',
            'target_time' => 'Hari Ini, 14:45 WIB (2 Jam)',
            'countdown' => 'Sisa 24 Menit',
            'countdown_type' => 'warning',
            'status' => 'Mendekati Batas',
            'status_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'action_label' => 'Eskalasi',
            'action_type' => 'link-primary',
        ],
        [
            'code' => '#TK-8919',
            'facility' => 'Kebocoran Pipa Saluran Air Bersih',
            'category' => 'Plumbing & Sanitasi',
            'location' => 'Gedung Operasional, Basemen',
            'priority' => 'High',
            'priority_style' => 'bg-amber-50 text-amber-700 border-amber-200',
            'technician' => 'Ahmad Fauzi',
            'target_time' => 'Hari Ini, 12:00 WIB (4 Jam)',
            'countdown' => 'Lewat 2j 15m',
            'countdown_type' => 'danger',
            'status' => 'Terlambat (Breached)',
            'status_style' => 'bg-rose-50 text-rose-700 border-rose-200',
            'action_label' => 'Investigasi',
            'action_type' => 'link-danger',
        ],
        [
            'code' => '#TK-8950',
            'facility' => 'Penggantian Saklar & Lampu Koridor Lt. 3',
            'category' => 'Electrical & Lighting',
            'location' => 'Gedung Utama, Koridor Timur',
            'priority' => 'Medium',
            'priority_style' => 'bg-blue-50 text-blue-700 border-blue-200',
            'technician' => 'Dimas Pratama',
            'target_time' => 'Besok, 09:00 WIB (12 Jam)',
            'countdown' => 'Sisa 10j 40m',
            'countdown_type' => 'normal',
            'status' => 'Aman (On-Track)',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'action_label' => 'Detail',
            'action_type' => 'button',
        ],
    ]
])

<div id="active-tickets-section" class="rounded-xl border border-border bg-card p-5 shadow-xs" x-data="{ activeTab: 'at-risk' }">
    <!-- Header & Filter Tabs -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-4">
        <div>
            <h3 class="text-sm font-bold text-foreground sm:text-base">
                Status Pelanggaran & Batas SLA Tiket Aktif
            </h3>
            <p class="mt-0.5 text-xs text-muted-foreground">
                Daftar laporan aktif dengan timer hitung mundur penyelesaian
            </p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-1.5 self-start sm:self-auto">
            <button
                type="button"
                @click="activeTab = 'all'"
                :class="activeTab === 'all'
                    ? 'bg-secondary text-foreground font-semibold shadow-2xs'
                    : 'text-muted-foreground hover:text-foreground font-medium'"
                class="rounded-md border border-border px-3 py-1 text-xs transition cursor-pointer"
            >
                Semua (24)
            </button>
            <button
                type="button"
                @click="activeTab = 'at-risk'"
                :class="activeTab === 'at-risk'
                    ? 'border-amber-300 bg-amber-50 text-amber-800 font-semibold shadow-2xs'
                    : 'text-muted-foreground hover:text-foreground font-medium border border-border'"
                class="rounded-md px-3 py-1 text-xs transition cursor-pointer"
            >
                Mendekati Batas (4)
            </button>
            <button
                type="button"
                @click="activeTab = 'breached'"
                :class="activeTab === 'breached'
                    ? 'border-rose-300 bg-rose-50 text-rose-800 font-semibold shadow-2xs'
                    : 'text-muted-foreground hover:text-foreground font-medium border border-border'"
                class="rounded-md px-3 py-1 text-xs transition cursor-pointer"
            >
                Terlambat (2)
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    <th scope="col" class="pb-3 pr-4 font-semibold">No. Tiket</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Fasilitas / Masalah</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Lokasi</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Prioritas</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Teknisi Penanggungjawab</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Target Waktu SLA</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Hitung Mundur</th>
                    <th scope="col" class="pb-3 px-3 font-semibold">Status SLA</th>
                    <th scope="col" class="pb-3 pl-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
                @foreach ($tickets as $item)
                    <tr class="transition hover:bg-muted/20">
                        <!-- No. Tiket -->
                        <td class="py-3.5 pr-4 font-mono font-bold text-primary whitespace-nowrap">
                            <a href="#" class="hover:underline">
                                {{ $item['code'] }}
                            </a>
                        </td>

                        <!-- Fasilitas / Masalah -->
                        <td class="py-3.5 px-3">
                            <div class="font-bold text-foreground">{{ $item['facility'] }}</div>
                            <div class="text-[10px] text-muted-foreground">{{ $item['category'] }}</div>
                        </td>

                        <!-- Lokasi -->
                        <td class="py-3.5 px-3 text-muted-foreground whitespace-nowrap">
                            {{ $item['location'] }}
                        </td>

                        <!-- Prioritas -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-[10px] font-bold {{ $item['priority_style'] }}">
                                {{ $item['priority'] }}
                            </span>
                        </td>

                        <!-- Teknisi Penanggungjawab -->
                        <td class="py-3.5 px-3 whitespace-nowrap font-medium text-foreground">
                            {{ $item['technician'] }}
                        </td>

                        <!-- Target Waktu SLA -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-muted-foreground">
                            {{ $item['target_time'] }}
                        </td>

                        <!-- Hitung Mundur -->
                        <td class="py-3.5 px-3 whitespace-nowrap font-bold">
                            @if ($item['countdown_type'] === 'warning')
                                <span class="inline-flex items-center gap-1 text-amber-600">
                                    <x-lucide-alert-triangle class="h-3 w-3" />
                                    {{ $item['countdown'] }}
                                </span>
                            @elseif ($item['countdown_type'] === 'danger')
                                <span class="inline-flex items-center gap-1 text-rose-600">
                                    <x-lucide-alert-circle class="h-3 w-3" />
                                    {{ $item['countdown'] }}
                                </span>
                            @else
                                <span class="text-muted-foreground">
                                    {{ $item['countdown'] }}
                                </span>
                            @endif
                        </td>

                        <!-- Status SLA -->
                        <td class="py-3.5 px-3 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[10px] font-semibold {{ $item['status_style'] }}">
                                @if (str_contains($item['status'], 'Aman'))
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                @endif
                                {{ $item['status'] }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 pl-3 text-right whitespace-nowrap">
                            @if ($item['action_type'] === 'link-primary')
                                <a href="#" class="font-bold text-primary hover:underline">
                                    {{ $item['action_label'] }}
                                </a>
                            @elseif ($item['action_type'] === 'link-danger')
                                <a href="#" class="font-bold text-rose-600 hover:underline">
                                    {{ $item['action_label'] }}
                                </a>
                            @else
                                <button type="button" class="rounded border border-border bg-background px-2.5 py-1 text-xs font-medium text-foreground hover:bg-secondary cursor-pointer">
                                    {{ $item['action_label'] }}
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
