@props(['parents' => [], 'regions' => []])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-background p-4 shadow-2xs']) }}>
    <form method="GET" action="{{ route('location-and-category.locations.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Search Keyword -->
            <div class="relative">
                <x-lucide-search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kode, nama, wilayah..."
                    class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
            </div>

            <!-- Region Filter -->
            <div>
                <select
                    name="region"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Wilayah</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region }}" @selected(request('region') === $region)>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Parent Location Filter -->
            <div>
                <select
                    name="parent_id"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Hierarki</option>
                    <option value="root" @selected(request('parent_id') === 'root')>Lokasi Induk Utama (Root)</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(request('parent_id') == $parent->id)>
                            Sub-lokasi: {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
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
        <div class="flex items-center gap-2 shrink-0">
            <button
                type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
            >
                <x-lucide-filter class="h-3.5 w-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->filled('search') || request()->filled('region') || request()->filled('parent_id') || request()->filled('is_active'))
                <a
                    href="{{ route('location-and-category.locations.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                >
                    <x-lucide-x class="h-3.5 w-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>
</div>
