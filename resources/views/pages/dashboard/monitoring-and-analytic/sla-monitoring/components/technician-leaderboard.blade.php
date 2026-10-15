@props([
    'technicians' => [
        [
            'avatar' => 'BS',
            'avatar_style' => 'bg-emerald-100 text-emerald-800',
            'name' => 'Budi Santoso',
            'specialization' => 'HVAC & Mechanical Specialist',
            'total_tickets' => 58,
            'on_time' => 57,
            'breached' => 1,
            'avg_response' => '8 Menit',
            'compliance' => '98.3%',
            'compliance_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        [
            'avatar' => 'DP',
            'avatar_style' => 'bg-blue-100 text-blue-800',
            'name' => 'Dimas Pratama',
            'specialization' => 'Electrical System Engineer',
            'total_tickets' => 64,
            'on_time' => 62,
            'breached' => 2,
            'avg_response' => '12 Menit',
            'compliance' => '96.9%',
            'compliance_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        [
            'avatar' => 'RH',
            'avatar_style' => 'bg-purple-100 text-purple-800',
            'name' => 'Rian Hidayat',
            'specialization' => 'Vertical Transport & Civil',
            'total_tickets' => 49,
            'on_time' => 46,
            'breached' => 3,
            'avg_response' => '14 Menit',
            'compliance' => '93.8%',
            'compliance_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        [
            'avatar' => 'AF',
            'avatar_style' => 'bg-amber-100 text-amber-800',
            'name' => 'Ahmad Fauzi',
            'specialization' => 'Plumbing & General Infrastructure',
            'total_tickets' => 42,
            'on_time' => 37,
            'breached' => 5,
            'avg_response' => '22 Menit',
            'compliance' => '88.1%',
            'compliance_style' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
    ]
])

<div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
    <div>
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-3">
            <div>
                <h3 class="text-sm font-bold text-foreground sm:text-base">
                    Evaluasi & Leaderboard Performa Teknisi (SLA)
                </h3>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Peringkat kedisiplinan dan pencapaian SLA penyelesaian penugasan
                </p>
            </div>
            <a href="#" class="text-xs font-semibold text-primary hover:underline self-start sm:self-auto">
                Semua Teknisi (18)
            </a>
        </div>

        <!-- Table -->
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                        <th scope="col" class="pb-3 pr-4 font-semibold">Teknisi</th>
                        <th scope="col" class="pb-3 px-3 font-semibold text-center">Total Tiket</th>
                        <th scope="col" class="pb-3 px-3 font-semibold text-center">Tepat Waktu</th>
                        <th scope="col" class="pb-3 px-3 font-semibold text-center">Breached</th>
                        <th scope="col" class="pb-3 px-3 font-semibold">Rata-rata Respon</th>
                        <th scope="col" class="pb-3 pl-3 text-right font-semibold">Kepatuhan SLA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/40">
                    @foreach ($technicians as $item)
                        <tr class="transition hover:bg-muted/20">
                            <!-- Teknisi Avatar & Name -->
                            <td class="py-3.5 pr-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $item['avatar_style'] }}">
                                        {{ $item['avatar'] }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-foreground">{{ $item['name'] }}</div>
                                        <div class="text-[10px] text-muted-foreground">{{ $item['specialization'] }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Total Tiket -->
                            <td class="py-3.5 px-3 text-center font-bold text-foreground">
                                {{ $item['total_tickets'] }}
                            </td>

                            <!-- Tepat Waktu -->
                            <td class="py-3.5 px-3 text-center font-bold text-emerald-600">
                                {{ $item['on_time'] }}
                            </td>

                            <!-- Breached -->
                            <td class="py-3.5 px-3 text-center font-bold text-rose-500">
                                {{ $item['breached'] }}
                            </td>

                            <!-- Rata-rata Respon -->
                            <td class="py-3.5 px-3 whitespace-nowrap font-medium text-foreground">
                                {{ $item['avg_response'] }}
                            </td>

                            <!-- Kepatuhan SLA Badge -->
                            <td class="py-3.5 pl-3 text-right whitespace-nowrap">
                                <span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-[11px] font-bold {{ $item['compliance_style'] }}">
                                    {{ $item['compliance'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
