@props(['severityHints' => []])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-card p-4 shadow-2xs']) }}>
    <form method="GET" action="{{ route('location-and-category.damage-categories.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <!-- Pencarian -->
            <div class="relative flex-1">
                <x-lucide-search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kode, nama kerusakan, tingkat urgensi, atau deskripsi..."
                    class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
            </div>

            <!-- Filter Tingkat Urgensi (Severity Hint) -->
            <div class="w-full sm:w-52">
                <select
                    name="severity_hint"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Tingkat Urgensi</option>
                    @foreach($severityHints as $hint)
                        <option value="{{ $hint }}" @selected(request('severity_hint') === $hint)>
                            {{ $hint }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="w-full sm:w-40">
                <select
                    name="is_active"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-2">
            <button
                type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors shadow-2xs"
            >
                <x-lucide-filter class="h-3.5 w-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->hasAny(['search', 'severity_hint', 'is_active']))
                <a
                    href="{{ route('location-and-category.damage-categories.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-foreground hover:bg-accent transition-colors"
                >
                    <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>
</div>
