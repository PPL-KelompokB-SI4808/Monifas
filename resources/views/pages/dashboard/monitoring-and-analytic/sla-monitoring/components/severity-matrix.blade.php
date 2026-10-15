@props([
    'levels' => [
        [
            'level' => 'Critical (P1)',
            'dot' => 'bg-rose-500',
            'target_response' => '≤ 15 Menit',
            'target_resolution' => '≤ 2 Jam',
            'response_rate' => '98.2%',
            'resolution_rate' => '92.4%',
            'bar_color' => 'bg-rose-500',
            'status' => 'Perhatian',
            'status_style' => 'bg-amber-50 text-amber-700 border-amber-200',
        ],
        [
            'level' => 'High (P2)',
            'dot' => 'bg-orange-500',
            'target_response' => '≤ 30 Menit',
            'target_resolution' => '≤ 4 Jam',
            'response_rate' => '99.1%',
            'resolution_rate' => '95.8%',
            'bar_color' => 'bg-orange-500',
            'status' => 'Aman',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        [
            'level' => 'Medium (P3)',
            'dot' => 'bg-amber-500',
            'target_response' => '≤ 1 Jam',
            'target_resolution' => '≤ 12 Jam',
            'response_rate' => '99.5%',
            'resolution_rate' => '97.6%',
            'bar_color' => 'bg-amber-500',
            'status' => 'Aman',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
        [
            'level' => 'Low (P4)',
            'dot' => 'bg-emerald-500',
            'target_response' => '≤ 2 Jam',
            'target_resolution' => '≤ 24 Jam',
            'response_rate' => '100.0%',
            'resolution_rate' => '99.2%',
            'bar_color' => 'bg-emerald-500',
            'status' => 'Aman',
            'status_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ],
    ]
])

<div class="rounded-xl border border-border bg-card p-5 shadow-xs flex flex-col justify-between">
    <div>
        <!-- Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-3">
            <div>
                <h3 class="text-sm font-bold text-foreground sm:text-base">
                    Matriks Target SLA Berdasarkan Keparahan
                </h3>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Target respon awal &amp; waktu resolusi operasional fasilitas
                </p>
            </div>
            <span class="inline-flex items-center rounded-sm border border-blue-200 bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-700 self-start sm:self-auto">
                Standar ISO 55001
            </span>
        </div>

        <!-- Table -->
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border/60 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                        <th scope="col" class="pb-3 pr-4 font-semibold">Tingkat Keparahan</th>
                        <th scope="col" class="pb-3 px-3 font-semibold">Target Respon</th>
                        <th scope="col" class="pb-3 px-3 font-semibold">Target Resolusi</th>
                        <th scope="col" class="pb-3 px-3 font-semibold">Kepatuhan Respon</th>
                        <th scope="col" class="pb-3 px-3 font-semibold">Kepatuhan Selesai</th>
                        <th scope="col" class="pb-3 pl-3 text-right font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/40">
                    @foreach ($levels as $item)
                        <tr class="transition hover:bg-muted/20">
                            <!-- Tingkat Keparahan -->
                            <td class="py-3.5 pr-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full {{ $item['dot'] }}"></span>
                                    <span class="font-bold text-foreground">{{ $item['level'] }}</span>
                                </div>
                            </td>

                            <!-- Target Respon -->
                            <td class="py-3.5 px-3 whitespace-nowrap text-muted-foreground font-mono">
                                {{ $item['target_response'] }}
                            </td>

                            <!-- Target Resolusi -->
                            <td class="py-3.5 px-3 whitespace-nowrap text-muted-foreground font-mono">
                                {{ $item['target_resolution'] }}
                            </td>

                            <!-- Kepatuhan Respon -->
                            <td class="py-3.5 px-3 whitespace-nowrap font-bold text-foreground">
                                {{ $item['response_rate'] }}
                            </td>

                            <!-- Kepatuhan Selesai + Mini Bar -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-bold text-foreground w-11">{{ $item['resolution_rate'] }}</span>
                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-muted">
                                        <div class="h-full rounded-full {{ $item['bar_color'] }}" style="width: {{ rtrim($item['resolution_rate'], '%') }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 pl-3 text-right whitespace-nowrap">
                                <span class="inline-flex items-center rounded-sm border px-2 py-0.5 text-[10px] font-bold {{ $item['status_style'] }}">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
