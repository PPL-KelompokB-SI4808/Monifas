<x-layouts.dashboard title="Ringkasan Kondisi Operasional">
    <div class="space-y-6 pb-8">
        <!-- 1. Header & Period Tabs -->
        <x-dashboard::header-filters />

        <!-- 2. Operational Filter Toolbar -->
        <x-dashboard::operational-filter />

        <!-- 3. 4-Column KPI Stats Overview -->
        <x-dashboard::stats-overview />

        <!-- 4. Middle Section: Trends Chart & SLA Effectiveness -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <x-dashboard::chart-trends />
            </div>
            <div class="lg:col-span-4">
                <x-dashboard::sla-effectiveness />
            </div>
        </div>

        <!-- 5. Lower Section: Status Distribution & Frequent Issues Table -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-dashboard::status-distribution />
            </div>
            <div class="lg:col-span-8">
                <x-dashboard::frequent-issues-table />
            </div>
        </div>

        <!-- 6. Bottom Section: Recent Activities & Incident Handling -->
        <x-dashboard::recent-activities-table />
    </div>
</x-layouts.dashboard>
