@props([
    'locations',
    'buildings',
    'rooms',
    'facilities',
    'damageCategories',
    'priorities',
    'report' => null,
])

<div class="space-y-6">
    <!-- Judul Laporan -->
    <div class="space-y-1.5">
        <label for="title" class="text-xs font-semibold text-foreground flex items-center justify-between">
            <span>Judul Laporan <span class="text-red-500">*</span></span>
            <span class="text-[11px] font-normal text-muted-foreground">Maks. 200 karakter</span>
        </label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $report?->title) }}"
            required
            maxlength="200"
            placeholder="Contoh: Kran Air Wastafel Patah di Toilet Lantai 2"
            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
        />
        @error('title')
            <p class="text-[11px] text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Lokasi & Fasilitas Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Lokasi Area -->
        <div class="space-y-1.5">
            <label for="location_id" class="text-xs font-semibold text-foreground">
                Lokasi / Area <span class="text-red-500">*</span>
            </label>
            <select
                id="location_id"
                name="location_id"
                required
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Pilih Area Kampus / Wilayah</option>
                @foreach ($locations as $location)
                    <option
                        value="{{ $location->id }}"
                        @selected(old('location_id', $report?->location_id) == $location->id)
                    >
                        {{ $location->name }} ({{ $location->code }})
                    </option>
                @endforeach
            </select>
            @error('location_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Gedung -->
        <div class="space-y-1.5">
            <label for="building_id" class="text-xs font-semibold text-foreground">
                Gedung <span class="text-red-500">*</span>
            </label>
            <select
                id="building_id"
                name="building_id"
                required
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Pilih Gedung</option>
                @foreach ($buildings as $building)
                    <option
                        value="{{ $building->id }}"
                        @selected(old('building_id', $report?->building_id) == $building->id)
                    >
                        {{ $building->name }} ({{ $building->code }})
                    </option>
                @endforeach
            </select>
            @error('building_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ruangan (Opsional) -->
        <div class="space-y-1.5">
            <label for="room_id" class="text-xs font-semibold text-foreground flex items-center justify-between">
                <span>Ruangan</span>
                <span class="text-[11px] font-normal text-muted-foreground">Opsional</span>
            </label>
            <select
                id="room_id"
                name="room_id"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Tanpa Ruangan Spesifik (Fasilitas Umum/Luar Ruangan)</option>
                @foreach ($rooms as $room)
                    <option
                        value="{{ $room->id }}"
                        @selected(old('room_id', $report?->room_id) == $room->id)
                    >
                        {{ $room->name }} (Lantai {{ $room->floor ?? '1' }})
                    </option>
                @endforeach
            </select>
            @error('room_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fasilitas / Aset -->
        <div class="space-y-1.5">
            <label for="facility_id" class="text-xs font-semibold text-foreground">
                Fasilitas / Aset Fisik <span class="text-red-500">*</span>
            </label>
            <select
                id="facility_id"
                name="facility_id"
                required
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Pilih Fasilitas yang Bermasalah</option>
                @foreach ($facilities as $facility)
                    <option
                        value="{{ $facility->id }}"
                        @selected(old('facility_id', $report?->facility_id) == $facility->id)
                    >
                        {{ $facility->name }} [{{ $facility->code }}]
                    </option>
                @endforeach
            </select>
            @error('facility_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Kategori Kerusakan & Prioritas SLA -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Kategori Kerusakan -->
        <div class="space-y-1.5">
            <label for="damage_category_id" class="text-xs font-semibold text-foreground">
                Kategori Kerusakan <span class="text-red-500">*</span>
            </label>
            <select
                id="damage_category_id"
                name="damage_category_id"
                required
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Pilih Jenis Kerusakan</option>
                @foreach ($damageCategories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('damage_category_id', $report?->damage_category_id) == $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('damage_category_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tingkat Prioritas SLA -->
        <div class="space-y-1.5">
            <label for="priority_id" class="text-xs font-semibold text-foreground flex items-center justify-between">
                <span>Tingkat Prioritas (SLA) <span class="text-red-500">*</span></span>
                <span class="text-[11px] font-normal text-muted-foreground">Target respon & resolusi</span>
            </label>
            <select
                id="priority_id"
                name="priority_id"
                required
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
            >
                <option value="">Pilih Tingkat Prioritas</option>
                @foreach ($priorities as $priority)
                    <option
                        value="{{ $priority->id }}"
                        @selected(old('priority_id', $report?->priority_id) == $priority->id)
                    >
                        {{ $priority->name }} (Respon: {{ $priority->sla_response_hours }}j, Selesai: {{ $priority->sla_resolution_hours }}j)
                    </option>
                @endforeach
            </select>
            @error('priority_id')
                <p class="text-[11px] text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Deskripsi Kerusakan -->
    <div class="space-y-1.5">
        <label for="description" class="text-xs font-semibold text-foreground flex items-center justify-between">
            <span>Deskripsi Rinci Kerusakan <span class="text-red-500">*</span></span>
            <span class="text-[11px] font-normal text-muted-foreground">Jelaskan kondisi & lokasi spesifik</span>
        </label>
        <textarea
            id="description"
            name="description"
            rows="4"
            required
            placeholder="Jelaskan secara detail masalah fasilitas yang terjadi, dampak yang ditimbulkan, dan petunjuk lokasi..."
            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
        >{{ old('description', $report?->description) }}</textarea>
        @error('description')
            <p class="text-[11px] text-red-500">{{ $message }}</p>
        @enderror
    </div>
</div>

