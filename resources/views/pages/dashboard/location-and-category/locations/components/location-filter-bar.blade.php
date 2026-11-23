@props(['parents' => [], 'regions' => []])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-background p-4']) }}>
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
                    class="w-full rounded-[6px] border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-[rgba(99,102,241,0.12)]"
                />
            </div>

            <!-- Region Filter -->
            <div>
                <select
                    name="region"
                    class="w-full rounded-[6px] border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-[rgba(99,102,241,0.12)]"
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
                    class="w-full rounded-[6px] border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-[rgba(99,102,241,0.12)]"
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
                    class="w-full rounded-[6px] border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-[rgba(99,102,241,0.12)]"
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
                class="inline-flex items-center gap-1.5 rounded-[6px] bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px cursor-pointer"
            >
                <x-lucide-filter class="h-3.5 w-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->filled('search') || request()->filled('region') || request()->filled('parent_id') || request()->filled('is_active'))
                <a
                    href="{{ route('location-and-category.locations.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground"
                >
                    <x-lucide-x class="h-3.5 w-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>
</div>
