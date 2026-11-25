<x-layouts.dashboard title="Laporan & Analisis Pemeliharaan">
    @php
        $locations = $filterOptions['locations'] ?? collect();
        $buildings = $filterOptions['buildings'] ?? collect();
        $facilityCategories = $filterOptions['facility_categories'] ?? collect();
        $statuses = $filterOptions['statuses'] ?? [];
    @endphp

    <div class="space-y-6 pb-8">
        <!-- Header & Export Trigger -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Laporan &amp; Analisis Pemeliharaan
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Analisis riwayat perbaikan, frekuensi kerusakan, rincian biaya material, dan rasio preventive vs reaktif.
                </p>
            </div>

            <x-monitoring-and-analytic-maintenance-report::export-report-modal :filters="$filters ?? []" />
        </div>

        <!-- Filter Toolbar -->
        <x-ui.card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('monitoring-and-analytic.maintenance-report.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-1">
                    <label for="mr-period" class="block text-xs font-medium text-muted-foreground">Periode Preset</label>
                    <select id="mr-period" name="period" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none">
                        <option value="">Semua Waktu</option>
                        <option value="month" @selected(($filters['period'] ?? '') === 'month')>Bulan Ini</option>
                        <option value="quarter" @selected(($filters['period'] ?? '') === 'quarter')>Kuartal Ini</option>
                        <option value="year" @selected(($filters['period'] ?? '') === 'year')>Tahun Ini</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="mr-start-date" class="block text-xs font-medium text-muted-foreground">Tanggal Mulai</label>
                    <input id="mr-start-date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none" />
                </div>

                <div class="space-y-1">
                    <label for="mr-end-date" class="block text-xs font-medium text-muted-foreground">Tanggal Akhir</label>
                    <input id="mr-end-date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none" />
                </div>

                <div class="space-y-1">
                    <label for="mr-location" class="block text-xs font-medium text-muted-foreground">Lokasi</label>
                    <select id="mr-location" name="location_id" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected((string) ($filters['location_id'] ?? '') === (string) $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="mr-building" class="block text-xs font-medium text-muted-foreground">Gedung</label>
                    <select id="mr-building" name="building_id" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none">
                        <option value="">Semua Gedung</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->id }}" @selected((string) ($filters['building_id'] ?? '') === (string) $building->id)>{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="mr-category" class="block text-xs font-medium text-muted-foreground">Kategori Fasilitas</label>
                    <select id="mr-category" name="facility_category_id" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($facilityCategories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['facility_category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="mr-status" class="block text-xs font-medium text-muted-foreground">Status Perbaikan</label>
                    <select id="mr-status" name="status" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ ucfirst(str_replace('_', ' ', $status->value)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" size="sm" class="flex-1">
                        <x-lucide-filter class="h-3.5 w-3.5" />
                        Filter
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" href="{{ route('monitoring-and-analytic.maintenance-report.index') }}">
                        Reset
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card :hover="true" class="p-5">
                <span class="text-xs font-medium text-muted-foreground">Total Perbaikan</span>
                <p class="mt-2 text-2xl font-bold text-foreground">{{ number_format((int) ($summary['total_repairs'] ?? 0)) }}</p>
            </x-ui.card>
            <x-ui.card :hover="true" class="p-5">
                <span class="text-xs font-medium text-muted-foreground">Perbaikan Selesai</span>
                <p class="mt-2 text-2xl font-bold text-success">{{ number_format((int) ($summary['completed_repairs'] ?? 0)) }}</p>
            </x-ui.card>
            <x-ui.card :hover="true" class="p-5">
                <span class="text-xs font-medium text-muted-foreground">Total Biaya Material</span>
                <p class="mt-2 text-2xl font-bold text-foreground">Rp {{ number_format((float) ($summary['total_cost'] ?? 0), 0, ',', '.') }}</p>
            </x-ui.card>
            <x-ui.card :hover="true" class="p-5">
                <span class="text-xs font-medium text-muted-foreground">Rata-rata Durasi</span>
                <p class="mt-2 text-2xl font-bold text-foreground">{{ number_format((float) ($summary['avg_duration_hours'] ?? 0), 1) }} jam</p>
            </x-ui.card>
        </div>

        <!-- Preventive vs Reactive & Damage Frequency -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-monitoring-and-analytic-maintenance-report::preventive-vs-reactive-chart
                    :preventive-vs-reactive="$preventiveVsReactive ?? []"
                />
            </div>
            <div class="lg:col-span-7">
                <x-monitoring-and-analytic-maintenance-report::frequency-analysis-card
                    :damage-frequency="$damageFrequency ?? []"
                />
            </div>
        </div>

        <!-- Cost & Material Breakdown -->
        <x-monitoring-and-analytic-maintenance-report::cost-breakdown-table
            :cost-analysis="$costAnalysis ?? []"
        />

        <!-- Maintenance & Repair History Table -->
        <x-ui.card class="p-5">
            <div class="border-b border-border pb-3">
                <h3 class="text-sm font-bold text-foreground sm:text-base">
                    Riwayat Pemeliharaan &amp; Perbaikan
                </h3>
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Daftar historis pekerjaan pemeliharaan preventif dan korektif
                </p>
            </div>

            @if ($history->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <x-lucide-clipboard-list class="h-8 w-8 text-muted-foreground" />
                    <p class="mt-2 text-sm font-medium text-foreground">Belum ada riwayat pemeliharaan</p>
                    <p class="mt-1 text-xs text-muted-foreground">Tidak ada catatan perbaikan yang sesuai dengan filter saat ini.</p>
                </div>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-border text-muted-foreground">
                                <th class="py-2.5 pr-3 font-semibold">ID</th>
                                <th class="px-3 py-2.5 font-semibold">Tanggal</th>
                                <th class="px-3 py-2.5 font-semibold">Fasilitas</th>
                                <th class="px-3 py-2.5 font-semibold">Jenis</th>
                                <th class="px-3 py-2.5 font-semibold">Teknisi</th>
                                <th class="px-3 py-2.5 text-right font-semibold">Biaya</th>
                                <th class="py-2.5 pl-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($history as $repair)
                                @php
                                    $isPreventive = $repair->report_id === null || ($repair->assignment?->maintenance_schedule_id !== null);
                                @endphp
                                <tr class="hover:bg-secondary/50">
                                    <td class="py-3 pr-3 font-mono text-foreground">REP-{{ $repair->id }}</td>
                                    <td class="px-3 py-3 text-muted-foreground">{{ $repair->started_at->format('d M Y') }}</td>
                                    <td class="px-3 py-3">
                                        <p class="font-medium text-foreground">{{ $repair->facility?->name ?? '-' }}</p>
                                        <p class="text-muted-foreground">{{ $repair->facility?->building?->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <x-ui.badge :variant="$isPreventive ? 'primary' : 'warning'">
                                            {{ $isPreventive ? 'Preventive' : 'Reaktif' }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-3 py-3 text-foreground">{{ $repair->officer?->user?->name ?? '-' }}</td>
                                    <td class="px-3 py-3 text-right font-medium text-foreground">
                                        Rp {{ number_format((float) $repair->materials->sum('subtotal'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 pl-3">
                                        <x-ui.badge variant="default">{{ $repair->status->value }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-t border-border pt-3">
                    {{ $history->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.dashboard>
