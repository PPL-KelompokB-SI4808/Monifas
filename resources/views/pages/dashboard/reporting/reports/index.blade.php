<x-layouts.dashboard title="Daftar Pelaporan Kerusakan Fasilitas">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Pelaporan Kerusakan Fasilitas</h1>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Kelola dan pantau seluruh laporan kerusakan infrastruktur, pelacakan siklus penanganan, dan verifikasi supervisor.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('reporting.reports.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-plus class="h-4 w-4" />
                    <span>Laporkan Kerusakan</span>
                </a>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success') || session('status'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400 shadow-2xs">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') ?? session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400 shadow-2xs">
                <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="rounded-xl border border-border bg-background p-4 shadow-2xs">
            <form action="{{ route('reporting.reports.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Search input -->
                    <div class="relative sm:col-span-2 lg:col-span-1">
                        <x-lucide-search class="absolute left-3 top-2.5 h-3.5 w-3.5 text-muted-foreground" />
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari tiket, judul..."
                            class="w-full rounded-lg border border-border bg-background pl-9 pr-3 py-2 text-xs text-foreground placeholder:text-muted-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
                        />
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <select
                            name="status"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
                        >
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Prioritas -->
                    <div>
                        <select
                            name="priority_id"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
                        >
                            <option value="">Semua Prioritas</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}" @selected(request('priority_id') == $priority->id)>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Lokasi -->
                    <div>
                        <select
                            name="location_id"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
                        >
                            <option value="">Semua Lokasi</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" @selected(request('location_id') == $loc->id)>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Aksi / Reset -->
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-secondary px-3 py-2 text-xs font-medium text-foreground hover:bg-secondary/80 transition-colors cursor-pointer"
                        >
                            <x-lucide-filter class="h-3.5 w-3.5" />
                            <span>Terapkan</span>
                        </button>

                        @if (request()->hasAny(['search', 'status', 'priority_id', 'location_id', 'damage_category_id']))
                            <a
                                href="{{ route('reporting.reports.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-border bg-background p-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                title="Reset Filter"
                            >
                                <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Laporan Komponen -->
        <x-reporting-reports::report-table :reports="$reports" />
    </div>
</x-layouts.dashboard>
