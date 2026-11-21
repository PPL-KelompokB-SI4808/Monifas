@props([
    'campaign' => null,
    'action',
    'method' => 'POST',
    'buildings' => collect(),
    'categories' => collect(),
])

@php
    use App\Enums\CampaignStatus;

    $startDateValue = old('start_date', $campaign?->start_date ? $campaign->start_date->format('Y-m-d') : now()->format('Y-m-d'));
    $endDateValue = old('end_date', $campaign?->end_date ? $campaign->end_date->format('Y-m-d') : now()->addDays(14)->format('Y-m-d'));
    $currentStatus = old('status', $campaign?->status instanceof CampaignStatus ? $campaign->status->value : ($campaign?->status ?? 'scheduled'));
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-5">
        <h3 class="text-sm font-semibold text-foreground pb-3 border-b border-border flex items-center gap-2">
            <x-lucide-megaphone class="h-4 w-4 text-primary" />
            <span>{{ $campaign ? 'Perbarui Data Kampanye' : 'Informasi Kampanye Baru' }}</span>
        </h3>

        {{-- Title --}}
        <div>
            <label for="title" class="block text-xs font-medium text-foreground">
                Judul Kampanye <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', $campaign?->title) }}"
                required
                maxlength="200"
                placeholder="Contoh: Kampanye Perawatan AC Berkala Semester 2"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('title')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- Building --}}
            <div>
                <label for="building_id" class="block text-xs font-medium text-foreground">
                    Gedung Target <span class="text-red-500">*</span>
                </label>
                <select
                    name="building_id"
                    id="building_id"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="" disabled {{ old('building_id', $campaign?->building_id) ? '' : 'selected' }}>Pilih Gedung</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->id }}" @selected(old('building_id', $campaign?->building_id) == $building->id)>
                            {{ $building->name }}
                        </option>
                    @endforeach
                </select>
                @error('building_id')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Facility Category --}}
            <div>
                <label for="facility_category_id" class="block text-xs font-medium text-foreground">
                    Kategori Fasilitas <span class="text-red-500">*</span>
                </label>
                <select
                    name="facility_category_id"
                    id="facility_category_id"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="" disabled {{ old('facility_category_id', $campaign?->facility_category_id) ? '' : 'selected' }}>Pilih Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('facility_category_id', $campaign?->facility_category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('facility_category_id')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Objective --}}
        <div>
            <label for="objective" class="block text-xs font-medium text-foreground">
                Tujuan Kampanye <span class="text-red-500">*</span>
            </label>
            <textarea
                name="objective"
                id="objective"
                rows="3"
                required
                placeholder="Jelaskan tujuan utama kampanye edukasi pemeliharaan ini..."
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >{{ old('objective', $campaign?->objective) }}</textarea>
            @error('objective')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Content --}}
        <div>
            <label for="content" class="block text-xs font-medium text-foreground">
                Konten Kampanye <span class="text-red-500">*</span>
            </label>
            <textarea
                name="content"
                id="content"
                rows="6"
                required
                placeholder="Tuliskan materi edukasi, panduan pemeliharaan, tips perawatan fasilitas..."
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >{{ old('content', $campaign?->content) }}</textarea>
            @error('content')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Banner Path --}}
        <div>
            <label for="banner_path" class="block text-xs font-medium text-foreground">
                Path Banner (opsional)
            </label>
            <input
                type="text"
                name="banner_path"
                id="banner_path"
                value="{{ old('banner_path', $campaign?->banner_path) }}"
                maxlength="255"
                placeholder="campaigns/banners/contoh.jpg"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Path relatif ke storage. Kosongkan jika tidak ada banner.</p>
            @error('banner_path')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Schedule & Status --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 pt-2 border-t border-border">
            {{-- Start Date --}}
            <div>
                <label for="start_date" class="block text-xs font-medium text-foreground">
                    Tanggal Mulai <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="start_date"
                    id="start_date"
                    value="{{ $startDateValue }}"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('start_date')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-xs font-medium text-foreground">
                    Tanggal Berakhir <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="end_date"
                    id="end_date"
                    value="{{ $endDateValue }}"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                <p class="mt-1 text-[11px] text-muted-foreground">Harus sama atau setelah tanggal mulai.</p>
                @error('end_date')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-xs font-medium text-foreground">
                    Status
                </label>
                <select
                    name="status"
                    id="status"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    @foreach (CampaignStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($currentStatus === $statusOption->value)>
                            {{ $statusOption->label() }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-muted-foreground">Otomatis disesuaikan dengan tanggal jika tidak dipilih manual.</p>
                @error('status')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('supporting.campaigns.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
        >
            Batal
        </a>
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
        >
            <x-lucide-check class="h-3.5 w-3.5" />
            <span>{{ $campaign ? 'Perbarui Kampanye' : 'Simpan Kampanye' }}</span>
        </button>
    </div>
</form>
