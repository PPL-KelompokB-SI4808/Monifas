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
                'url' => route('location-and-category.locations.index'),
                'is_active' => request()->is('dashboard/location-and-category/locations*'),
            ],
            [
                'title' => 'Facility Categories',
                'slug' => 'facility-categories',
                'url' => route('location-and-category.facility-categories.index'),
                'is_active' => request()->is('dashboard/location-and-category/facility-categories*'),
            ],
            [
                'title' => 'Damage Categories',
                'slug' => 'damage-categories',
                'url' => route('location-and-category.damage-categories.index'),
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
    {{-- Brand --}}
    <div class="flex h-14 items-center justify-between border-b border-border px-4">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-2.5 rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            {{-- Icon mark --}}
            <img src="{{ asset('images/monifas-icon-only.svg') }}"
                 alt="MONIFAS"
                 class="h-7 w-7 shrink-0" />
            {{-- Wordmark (text only, since icon is separate) --}}
            <div class="flex flex-col leading-none">
                <span class="font-display text-sm font-bold text-foreground" style="letter-spacing: -0.03em;">MONIFAS</span>
                <span class="mt-0.5 text-[10px] text-muted-foreground">Facility Monitoring</span>
            </div>
        </a>

        {{-- Mobile close --}}
        <button
            type="button"
            class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground md:hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
            @click="sidebarOpen = false"
            aria-label="Tutup sidebar"
        >
            <x-lucide-x class="h-4 w-4" />
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3" aria-label="Navigasi Dashboard">
        <div class="space-y-0.5">
            @foreach ($modules as $module)
                @php
                    $isModuleActive = ($module['slug'] === 'monitoring-and-analytic' && request()->is('dashboard'))
                        || request()->is('dashboard/' . $module['slug'] . '*');
                @endphp
                <div x-data="{ open: {{ $isModuleActive ? 'true' : 'false' }} }">
                    {{-- Module trigger --}}
                    <button
                        type="button"
                        @click="open = !open"
                        class="group flex w-full items-center justify-between rounded-md px-2.5 py-1.5 text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring
                            {{ $isModuleActive
                                ? 'bg-secondary text-foreground'
                                : 'text-muted-foreground hover:bg-secondary/70 hover:text-foreground' }}"
                    >
                        <span class="flex items-center gap-2">
                            <x-dynamic-component
                                :component="$module['icon']"
                                class="h-3.5 w-3.5 shrink-0 {{ $isModuleActive ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground' }}"
                            />
                            {{ $module['name'] }}
                        </span>
                        <x-lucide-chevron-right
                            class="h-3 w-3 text-muted-foreground/60 transition-transform duration-150"
                            ::class="open ? 'rotate-90' : ''"
                        />
                    </button>

                    {{-- Submodules --}}
                    <div
                        x-show="open"
                        x-cloak
                        class="ml-3 mt-0.5 space-y-0.5 border-l border-border pl-3 pb-1"
                    >
                        @foreach ($module['submodules'] as $sub)
                            <a
                                href="{{ $sub['url'] }}"
                                class="flex items-center rounded-md px-2 py-1 text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring
                                    {{ $sub['is_active']
                                        ? 'bg-primary/10 text-primary font-semibold'
                                        : 'text-muted-foreground hover:bg-secondary/60 hover:text-foreground' }}"
                            >
                                {{ $sub['title'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </nav>

    {{-- Footer --}}
    <div class="border-t border-border p-3">
        <a
            href="/"
            class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
        >
            <x-lucide-arrow-left class="h-3.5 w-3.5" />
            Kembali ke Beranda
        </a>
    </div>
</aside>
