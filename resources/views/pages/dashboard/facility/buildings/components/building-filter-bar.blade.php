@props(['locations' => []])

<div class="rounded-xl border border-border bg-background p-4">
    <form method="GET" action="{{ route('facility.buildings.index') }}"
          class="flex flex-col gap-3 md:flex-row md:items-center">

        {{-- Search --}}
        <div class="relative flex-1">
            <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari kode atau nama gedung…"
                class="h-9 w-full rounded-lg border border-border bg-background py-0 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors"
            />
        </div>

        {{-- Location --}}
        <select
            name="location_id"
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors sm:w-44"
        >
            <option value="">Semua Lokasi</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>
                    {{ $location->name }}
                </option>
            @endforeach
        </select>

        {{-- Status --}}
        <select
            name="is_active"
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors sm:w-36"
        >
            <option value="">Semua Status</option>
            <option value="1" @selected(request('is_active') === '1')>Aktif</option>
            <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
        </select>

        {{-- Actions --}}
        <div class="flex shrink-0 items-center gap-2">
            <button type="submit"
                class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-4 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90 cursor-pointer">
                <x-lucide-filter class="h-3.5 w-3.5" />
                <span>Filter</span>
            </button>

            @if (request()->filled('search') || request()->filled('location_id') || request()->filled('is_active'))
                <a href="{{ route('facility.buildings.index') }}"
                   class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-border bg-background px-3 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
                    <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>
</div>
