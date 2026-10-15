<x-layouts.dashboard title="Laporan & Analisis Pemeliharaan">
    <div class="space-y-6 pb-8">
        <!-- 1. Header & Quick Actions -->
        <x-maintenance-report::header />

        <!-- 2. KPI Summary Cards -->
        <x-maintenance-report::kpi-metrics />

        <!-- 3. Middle Section: Facility Breakdown & Cost Analysis -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-maintenance-report::facility-breakdown />
            </div>
            <div class="lg:col-span-7">
                <x-maintenance-report::cost-analysis />
            </div>
        </div>

        <!-- 4. Bottom Section: Maintenance Activity History Table -->
        <x-maintenance-report::maintenance-history-table />
    </div>
</x-layouts.dashboard>
