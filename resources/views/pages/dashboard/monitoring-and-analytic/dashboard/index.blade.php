<x-layouts.dashboard title="Ringkasan Kondisi Operasional">
    <div class="space-y-6 pb-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                    Ringkasan Kondisi Operasional
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Pemantauan KPI fasilitas, tren volume laporan periodik, dan performa tim teknisi.
                </p>
            </div>
        </div>

        <x-monitoring-and-analytic-dashboard::dashboard-filter-bar
            :filters="$filters ?? []"
            :filter-options="$filterOptions ?? []"
        />

        <x-monitoring-and-analytic-dashboard::kpi-summary-cards :kpi="$kpi ?? []" />

        <x-monitoring-and-analytic-dashboard::operational-trend-chart :trends="$trends ?? []" />

        <x-monitoring-and-analytic-dashboard::team-performance-table
            :technician-performance="$technicianPerformance ?? []"
        />
    </div>
</x-layouts.dashboard>
