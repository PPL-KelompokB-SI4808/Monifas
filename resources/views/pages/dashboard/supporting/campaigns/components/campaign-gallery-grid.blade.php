@props([
    'campaigns',
    'filters' => [],
    'buildings' => collect(),
    'categories' => collect(),
])

@php
    use App\Enums\CampaignStatus;
@endphp

<div class="space-y-5">
    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('supporting.campaigns.index') }}" class="rounded-xl border border-border bg-background p-4 shadow-2xs">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label for="search" class="sr-only">Cari kampanye</label>
                <div class="relative">
                    <x-lucide-search class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari judul atau konten kampanye..."
                        class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="sr-only">Status</label>
                <select
                    name="status"
                    id="status"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Status</option>
                    @foreach (CampaignStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected(($filters['status'] ?? '') === $statusOption->value)>
                            {{ $statusOption->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Building --}}
            <div>
                <label for="building_id" class="sr-only">Gedung</label>
                <select
                    name="building_id"
                    id="building_id"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Gedung</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->id }}" @selected(($filters['building_id'] ?? '') == $building->id)>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-search class="h-3.5 w-3.5" />
                    <span>Filter</span>
                </button>
                <a
                    href="{{ route('supporting.campaigns.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                >
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Gallery Grid --}}
    @if ($campaigns->count() > 0)
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($campaigns as $campaign)
                <x-supporting-campaigns::campaign-card :campaign="$campaign" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $campaigns->withQueryString()->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-muted/20 py-16 text-center">
            <x-lucide-megaphone class="h-10 w-10 text-muted-foreground/40" />
            <p class="mt-3 text-sm font-medium text-muted-foreground">Belum ada kampanye edukasi</p>
            <p class="mt-1 text-xs text-muted-foreground/70">Buat kampanye baru untuk memulai edukasi pemeliharaan fasilitas.</p>
            <a
                href="{{ route('supporting.campaigns.create') }}"
                class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
            >
                <x-lucide-plus class="h-3.5 w-3.5" />
                <span>Buat Kampanye</span>
            </a>
        </div>
    @endif
</div>
