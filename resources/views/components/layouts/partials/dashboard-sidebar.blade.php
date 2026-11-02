@php
$modules = [
    [
        'name' => 'Monitoring',
        'slug' => 'monitoring-and-analytic',
        'icon' => 'lucide-chart-column',
        'submodules' => [
            [
                'title' => 'Dashboard',
                'slug' => 'dashboard',
                'url' => url('/dashboard/monitoring-and-analytic/dashboard'),
                'is_active' => request()->is('dashboard') || request()->is('dashboard/monitoring-and-analytic/dashboard*'),
            ],
            [
                'title' => 'Maintenance Reports',
                'slug' => 'maintenance-report',
                'url' => url('/dashboard/monitoring-and-analytic/maintenance-report'),
                'is_active' => request()->is('dashboard/monitoring-and-analytic/maintenance-report*'),
            ],
            [
                'title' => 'SLA Monitoring',
                'slug' => 'sla-monitoring',
                'url' => url('/dashboard/monitoring-and-analytic/sla-monitoring'),
                'is_active' => request()->is('dashboard/monitoring-and-analytic/sla-monitoring*'),
            ],
        ],
    ],
    [
        'name' => 'Facility',
        'slug' => 'facility',
        'icon' => 'lucide-building-2',
        'submodules' => [
            [
                'title' => 'Buildings',
                'slug' => 'buildings',
                'url' => route('facility.buildings.index'),
                'is_active' => request()->is('dashboard/buildings*'),
            ],
            [
                'title' => 'Rooms',
                'slug' => 'rooms',
                'url' => route('facility.rooms.index'),
                'is_active' => request()->is('dashboard/rooms*'),
            ],
            [
                'title' => 'Facilities',
                'slug' => 'facilities',
                'url' => route('facility.facilities.index'),
                'is_active' => request()->is('dashboard/facilities*'),
            ],
        ],
    ],
    [
        'name' => 'Location & Category',
        'slug' => 'location-and-category',
        'icon' => 'lucide-map-pin',
        'submodules' => [
            [
                'title' => 'Locations',
                'slug' => 'locations',
                'url' => url('/dashboard/location-and-category/locations'),
                'is_active' => request()->is('dashboard/location-and-category/locations*'),
            ],
            [
                'title' => 'Facility Categories',
                'slug' => 'facility-categories',
                'url' => url('/dashboard/location-and-category/facility-categories'),
                'is_active' => request()->is('dashboard/location-and-category/facility-categories*'),
            ],
            [
                'title' => 'Damage Categories',
                'slug' => 'damage-categories',
                'url' => url('/dashboard/location-and-category/damage-categories'),
                'is_active' => request()->is('dashboard/location-and-category/damage-categories*'),
            ],
        ],
    ],
    [
        'name' => 'Reporting',
        'slug' => 'reporting',
        'icon' => 'lucide-file-text',
        'submodules' => [
            [
                'title' => 'Damage Reports',
                'slug' => 'reports',
                'url' => url('/dashboard/reporting/reports'),
                'is_active' => request()->is('dashboard/reporting/reports*'),
            ],
            [
                'title' => 'Report Evidence',
                'slug' => 'report-evidence',
                'url' => url('/dashboard/reporting/report-evidence'),
                'is_active' => request()->is('dashboard/reporting/report-evidence*'),
            ],
            [
                'title' => 'Report Priorities',
                'slug' => 'report-priorities',
                'url' => url('/dashboard/reporting/report-priorities'),
                'is_active' => request()->is('dashboard/reporting/report-priorities*'),
            ],
        ],
    ],
    [
        'name' => 'Maintenance',
        'slug' => 'maintenance',
        'icon' => 'lucide-hard-hat',
        'submodules' => [
            [
                'title' => 'Officers',
                'slug' => 'officers',
                'url' => url('/dashboard/maintenance/officers'),
                'is_active' => request()->is('dashboard/maintenance/officers*'),
            ],
            [
                'title' => 'Assignments',
                'slug' => 'assignments',
                'url' => url('/dashboard/maintenance/assignments'),
                'is_active' => request()->is('dashboard/maintenance/assignments*'),
            ],
            [
                'title' => 'Schedules',
                'slug' => 'schedules',
                'url' => url('/dashboard/maintenance/schedules'),
                'is_active' => request()->is('dashboard/maintenance/schedules*'),
            ],
        ],
    ],
    [
        'name' => 'Repair',
        'slug' => 'repair',
        'icon' => 'lucide-wrench',
        'submodules' => [
            [
                'title' => 'Repairs',
                'slug' => 'repairs',
                'url' => url('/dashboard/repair/repairs'),
                'is_active' => request()->is('dashboard/repair/repairs*'),
            ],
            [
                'title' => 'Materials',
                'slug' => 'materials',
                'url' => url('/dashboard/repair/materials'),
                'is_active' => request()->is('dashboard/repair/materials*'),
            ],
            [
                'title' => 'Costs',
                'slug' => 'costs',
                'url' => url('/dashboard/repair/costs'),
                'is_active' => request()->is('dashboard/repair/costs*'),
            ],
        ],
    ],
    [
        'name' => 'Supporting',
        'slug' => 'supporting',
        'icon' => 'lucide-life-buoy',
        'submodules' => [
            [
                'title' => 'Announcements',
                'slug' => 'announcements',
                'url' => url('/dashboard/supporting/announcements'),
                'is_active' => request()->is('dashboard/supporting/announcements*'),
            ],
            [
                'title' => 'Feedback',
                'slug' => 'feedback',
                'url' => url('/dashboard/supporting/feedback'),
                'is_active' => request()->is('dashboard/supporting/feedback*'),
            ],
            [
                'title' => 'Campaigns',
                'slug' => 'campaigns',
                'url' => url('/dashboard/supporting/campaigns'),
                'is_active' => request()->is('dashboard/supporting/campaigns*'),
            ],
        ],
    ],
    [
        'name' => 'Users & Access',
        'slug' => 'users-and-access',
        'icon' => 'lucide-users',
        'submodules' => [
            [
                'title' => 'Users',
                'slug' => 'users',
                'url' => url('/dashboard/users-and-access/users'),
                'is_active' => request()->is('dashboard/users-and-access/users*'),
            ],
            [
                'title' => 'Roles',
                'slug' => 'roles',
                'url' => url('/dashboard/users-and-access/roles'),
                'is_active' => request()->is('dashboard/users-and-access/roles*'),
            ],
            [
                'title' => 'User Addresses',
                'slug' => 'user-addresses',
                'url' => route('users-and-access.user-addresses.index'),
                'is_active' => request()->is('dashboard/users-and-access/user-addresses*'),
            ],
            [
                'title' => 'Notifications',
                'slug' => 'notifications',
                'url' => route('users-and-access.notifications.index'),
                'is_active' => request()->is('dashboard/users-and-access/notifications*'),
            ],
        ],
    ],
];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-border bg-background transition-transform duration-200 ease-in-out md:static md:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Brand Header -->
    <div class="flex h-14 items-center justify-between border-b border-border px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded-md">
            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-primary text-primary-foreground text-xs font-semibold shadow-xs">
                M
            </span>
            <div class="flex flex-col">
                <span class="text-sm font-semibold tracking-tight text-foreground leading-none">MONIFAS</span>
                <span class="text-[11px] text-muted-foreground mt-0.5">Facility Monitoring</span>
            </div>
        </a>

        <!-- Mobile close button -->
        <button
            type="button"
            class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground md:hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
            @click="sidebarOpen = false"
            aria-label="Close sidebar"
        >
            <x-lucide-x class="h-4 w-4" />
        </button>
    </div>

    <!-- Navigation List (8 Modules) -->
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1" aria-label="Dashboard Navigation">

        <div class="space-y-1 pt-1">
            @foreach ($modules as $module)
                @php
                    $isModuleActive = ($module['slug'] === 'monitoring-and-analytic' && request()->is('dashboard'))
                        || request()->is('dashboard/' . $module['slug'] . '*');
                @endphp
                <div
                    x-data="{ open: {{ $isModuleActive ? 'true' : 'false' }} }"
                    class="space-y-0.5"
                >
                    <!-- Module Accordion Trigger -->
                    <button
                        type="button"
                        @click="open = !open"
                        class="group flex w-full items-center justify-between rounded-md px-2.5 py-1.5 text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {{ $isModuleActive ? 'text-foreground font-semibold bg-muted/40' : 'text-muted-foreground hover:bg-secondary/70 hover:text-foreground' }}"
                    >
                        <div class="flex items-center gap-2.5">
                            <x-dynamic-component
                                :component="$module['icon']"
                                class="h-4 w-4 shrink-0 transition-colors {{ $isModuleActive ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground' }}"
                            />
                            <span>{{ $module['name'] }}</span>
                        </div>
                        <x-lucide-chevron-right
                            class="h-3.5 w-3.5 text-muted-foreground/70 transition-transform duration-200"
                            ::class="open ? 'rotate-90 text-foreground' : ''"
                        />
                    </button>

                    <!-- Submodules List -->
                    <div
                        x-show="open"
                        x-cloak
                        class="ml-3.5 space-y-0.5 border-l border-border/70 pl-2.5 py-0.5"
                    >
                        @foreach ($module['submodules'] as $sub)
                            <a
                                href="{{ $sub['url'] }}"
                                class="group flex items-center justify-between rounded-md px-2 py-1 text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {{ $sub['is_active'] ? 'bg-secondary text-foreground font-semibold shadow-2xs' : 'text-muted-foreground hover:bg-secondary/60 hover:text-foreground' }}"
                            >
                                <span>{{ $sub['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="border-t border-border p-3">
        <a
            href="/"
            class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            <x-lucide-arrow-left class="h-3.5 w-3.5" />
            <span>Back to Home</span>
        </a>
    </div>
</aside>
