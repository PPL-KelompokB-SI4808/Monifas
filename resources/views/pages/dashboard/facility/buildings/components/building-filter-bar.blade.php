@props(['locations' => []])

<div class="rounded-xl border border-border bg-background p-4 shadow-2xs">
    <form method="GET" action="{{ route('facility.buildings.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            <!-- Search Keyword -->
            <div class="relative flex-1">
                <x-lucide-search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kode atau nama gedung..."
                    class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
            </div>

            <!-- Location Filter -->
            <div class="w-full sm:w-48">
                <select
                    name="location_id"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-36">
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

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <button
                type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
            >
                <x-lucide-filter class="h-3.5 w-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->filled('search') || request()->filled('location_id') || request()->filled('is_active'))
                <a
                    href="{{ route('facility.buildings.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                >
                    <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>
</div>
