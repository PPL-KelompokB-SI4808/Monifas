<x-layouts.dashboard title="SLA Performance & Real-time Monitoring">
    <div class="space-y-6 pb-8">
        <!-- 1. Header & Filters -->
        <x-sla-monitoring::header />

        <!-- 2. Alert Notice Banner -->
        <x-sla-monitoring::alert-banner />

        <!-- 3. KPI Cards -->
        <x-sla-monitoring::kpi-cards />

        <!-- 4. Middle Section: Severity Target Matrix & Overall SLA Score -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <x-sla-monitoring::severity-matrix />
            </div>
            <div class="lg:col-span-4">
                <x-sla-monitoring::sla-score-calculator />
            </div>
        </div>

        <!-- 5. Active Tickets & Countdown Table -->
        <x-sla-monitoring::active-tickets-table />

        <!-- 6. Bottom Section: Technician Leaderboard & Workload Distribution -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <x-sla-monitoring::technician-leaderboard />
            </div>
            <div class="lg:col-span-4">
                <x-sla-monitoring::workload-distribution />
            </div>
        </div>
    </div>
</x-layouts.dashboard>
