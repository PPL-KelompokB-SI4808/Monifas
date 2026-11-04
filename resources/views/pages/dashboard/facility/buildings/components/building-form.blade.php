@props(['building' => null, 'locations' => [], 'areaZones' => []])

<div class="space-y-5">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Lokasi Kawasan -->
        <div>
            <label for="location_id" class="block text-xs font-medium text-foreground">
                Lokasi Kawasan <span class="text-red-500">*</span>
            </label>
            <select
                name="location_id"
                id="location_id"
                required
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Pilih Lokasi Kawasan</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(old('location_id', $building?->location_id) == $location->id)>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            @error('location_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Zona Area -->
        <div>
            <label for="area_zone_id" class="block text-xs font-medium text-foreground">
                Zona Area <span class="text-xs text-muted-foreground">(Opsional)</span>
            </label>
            <select
                name="area_zone_id"
                id="area_zone_id"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Pilih Zona Area</option>
                @foreach($areaZones as $zone)
                    <option value="{{ $zone->id }}" @selected(old('area_zone_id', $building?->area_zone_id) == $zone->id)>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
            @error('area_zone_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Kode Gedung -->
        <div>
            <label for="code" class="block text-xs font-medium text-foreground">
                Kode Gedung <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="code"
                id="code"
                value="{{ old('code', $building?->code) }}"
                required
                maxlength="30"
                placeholder="Contoh: BLD-01"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('code')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama Gedung -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Gedung <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $building?->name) }}"
                required
                maxlength="150"
                placeholder="Contoh: Gedung Rektorat"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Total Lantai -->
    <div>
        <label for="total_floors" class="block text-xs font-medium text-foreground">
            Total Lantai <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="total_floors"
            id="total_floors"
            value="{{ old('total_floors', $building?->total_floors ?? 1) }}"
            required
            min="1"
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        />
        @error('total_floors')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Deskripsi -->
    <div>
        <label for="description" class="block text-xs font-medium text-foreground">
            Deskripsi Gedung <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <textarea
            name="description"
            id="description"
            rows="3"
            placeholder="Keterangan tambahan seputar peruntukan dan kondisi gedung..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        >{{ old('description', $building?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Keaktifan -->
    <div class="flex items-center gap-2 pt-2">
        <input type="hidden" name="is_active" value="0">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            @checked(old('is_active', $building?->is_active ?? true))
            class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
        />
        <label for="is_active" class="text-xs font-medium text-foreground cursor-pointer select-none">
            Gedung Aktif (Dapat digunakan untuk operasional & ruangan)
        </label>
    </div>
</div>
