@props([
    'facilityCategories' => [],
    'technicians' => [],
    'filters' => [],
])

<form method="GET" action="{{ route('supporting.feedback.index') }}" class="rounded-xl border border-border bg-card p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Search Input -->
        <div>
            <label for="search" class="block text-xs font-medium text-foreground mb-1">Cari Kata Kunci</label>
            <input
                type="text"
                name="search"
                id="search"
                value="{{ $filters['search'] ?? '' }}"
                placeholder="No Tiket, komentar, pelapor..."
                class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
            >
        </div>

        <!-- Rating Filter -->
        <div>
            <label for="rating" class="block text-xs font-medium text-foreground mb-1">Rating Bintang</label>
            <select
                name="rating"
                id="rating"
                class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
            >
                <option value="">Semua Rating (1-5)</option>
                @foreach(range(5, 1) as $star)
                    <option value="{{ $star }}" @selected(($filters['rating'] ?? '') == $star)>
                        ⭐ {{ $star }} Bintang
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Facility Category Filter -->
        <div>
            <label for="facility_category_id" class="block text-xs font-medium text-foreground mb-1">Kategori Fasilitas</label>
            <select
                name="facility_category_id"
                id="facility_category_id"
                class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
            >
                <option value="">Semua Kategori</option>
                @foreach($facilityCategories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['facility_category_id'] ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Technician Filter -->
        <div>
            <label for="officer_id" class="block text-xs font-medium text-foreground mb-1">Teknisi Penanggung Jawab</label>
            <select
                name="officer_id"
                id="officer_id"
                class="w-full rounded-md border border-input bg-background px-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
            >
                <option value="">Semua Teknisi</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}" @selected(($filters['officer_id'] ?? '') == $tech->id)>
                        {{ $tech->user?->name ?? $tech->employee_code }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mt-3 flex items-center justify-end gap-2 border-t border-border pt-3">
        @if(!empty(array_filter($filters)))
            <a
                href="{{ route('supporting.feedback.index') }}"
                class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-xs font-medium border border-border bg-background text-foreground hover:bg-secondary transition-colors"
            >
                Reset Filter
            </a>
        @endif
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-1.5 rounded-md px-4 py-1.5 text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
        >
            <x-lucide-filter class="h-3.5 w-3.5" />
            Terapkan Filter
        </button>
    </div>
</form>
