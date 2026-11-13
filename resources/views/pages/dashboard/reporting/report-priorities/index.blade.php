<x-layouts.dashboard title="Manajemen Master Prioritas & SLA Laporan">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Master Prioritas & SLA Laporan</h1>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Konfigurasi tingkat urgensi, batas waktu respon (SLA Response), waktu penyelesaian (SLA Resolution), dan penyesuaian urutan laporan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('reporting.report-priorities.sync-order') }}" method="POST" class="inline-block">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground shadow-2xs hover:bg-secondary hover:text-foreground transition-colors cursor-pointer"
                        title="Urutkan kembali otomatis dari Darurat -> Tinggi -> Sedang -> Rendah"
                    >
                        <x-lucide-arrow-down-up class="h-3.5 w-3.5" />
                        <span>Selaraskan Urutan</span>
                    </button>
                </form>

                <a
                    href="{{ route('reporting.report-priorities.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-plus class="h-3.5 w-3.5" />
                    <span>Tambah Prioritas</span>
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

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400 shadow-2xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Ringkasan SLA Cards Component -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                    Ringkasan Matriks SLA Sesuai Tingkat Urgensi
                </h2>
                <span class="text-[11px] text-muted-foreground">Urutan Otomatis: Darurat &rarr; Rendah</span>
            </div>
            <x-reporting-report-priorities::priority-sla-cards :priorities="$summaryPriorities" />
        </div>

        <!-- Filter Bar -->
        <div class="rounded-xl border border-border bg-background p-4 shadow-2xs">
            <form action="{{ route('reporting.report-priorities.index') }}" method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                <!-- Search Input -->
                <div class="sm:col-span-2">
                    <div class="relative">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama atau tingkat prioritas..."
                            class="w-full rounded-lg border border-border bg-background pl-9 pr-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                        />
                    </div>
                </div>

                <!-- Filter Level -->
                <div>
                    <select
                        name="level"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Level</option>
                        @foreach($levels as $lvl)
                            @php
                                $val = $lvl instanceof \App\Enums\PriorityLevel ? $lvl->value : $lvl;
                                $lbl = $lvl instanceof \App\Enums\PriorityLevel ? $lvl->label() : ucfirst($lvl);
                            @endphp
                            <option value="{{ $val }}" @selected(request('level') === $val)>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status & Submit -->
                <div class="flex items-center gap-2">
                    <select
                        name="is_active"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Status</option>
                        <option value="true" @selected(request('is_active') === 'true')>Aktif</option>
                        <option value="false" @selected(request('is_active') === 'false')>Nonaktif</option>
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-secondary px-3.5 py-2 text-xs font-medium text-foreground hover:bg-secondary/80 transition-colors cursor-pointer"
                        title="Terapkan Filter"
                    >
                        <x-lucide-filter class="h-3.5 w-3.5" />
                    </button>

                    @if(request()->hasAny(['search', 'level', 'is_active']))
                        <a
                            href="{{ route('reporting.report-priorities.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-2.5 py-2 text-xs text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                            title="Reset Filter"
                        >
                            <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Prioritas Component -->
        <div>
            <x-reporting-report-priorities::priority-table :priorities="$priorities" />
        </div>
    </div>
</x-layouts.dashboard>
