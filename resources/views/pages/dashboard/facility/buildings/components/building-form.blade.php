@props(['building' => null, 'locations' => [], 'areaZones' => []])

@php
    $inputClass = 'h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors';
    $selectClass = 'h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors';
    $labelClass  = 'block text-xs font-medium text-foreground';
    $errorClass  = 'mt-1 text-xs text-red-600';
@endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        {{-- Lokasi --}}
        <div>
            <label for="location_id" class="{{ $labelClass }}">
                Lokasi Kawasan <span class="text-red-500">*</span>
            </label>
            <select name="location_id" id="location_id" required class="mt-1.5 {{ $selectClass }}">
                <option value="">Pilih lokasi…</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}"
                        @selected(old('location_id', $building?->location_id) == $location->id)>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            @error('location_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- Zona Area --}}
        <div>
            <label for="area_zone_id" class="{{ $labelClass }}">
                Zona Area
                <span class="ml-1 text-muted-foreground font-normal">(opsional)</span>
            </label>
            <select name="area_zone_id" id="area_zone_id" class="mt-1.5 {{ $selectClass }}">
                <option value="">Pilih zona…</option>
                @foreach ($areaZones as $zone)
                    <option value="{{ $zone->id }}"
                        @selected(old('area_zone_id', $building?->area_zone_id) == $zone->id)>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
            @error('area_zone_id')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        {{-- Kode --}}
        <div>
            <label for="code" class="{{ $labelClass }}">
                Kode Gedung <span class="text-red-500">*</span>
            </label>
            <input type="text" name="code" id="code"
                value="{{ old('code', $building?->code) }}"
                required maxlength="30" placeholder="Contoh: BLD-01"
                class="mt-1.5 {{ $inputClass }} font-mono" />
            @error('code')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama --}}
        <div>
            <label for="name" class="{{ $labelClass }}">
                Nama Gedung <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" id="name"
                value="{{ old('name', $building?->name) }}"
                required maxlength="150" placeholder="Contoh: Gedung Rektorat"
                class="mt-1.5 {{ $inputClass }}" />
            @error('name')
                <p class="{{ $errorClass }}">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Total Lantai --}}
    <div class="sm:w-1/2 sm:pr-2.5">
        <label for="total_floors" class="{{ $labelClass }}">
            Total Lantai <span class="text-red-500">*</span>
        </label>
        <input type="number" name="total_floors" id="total_floors"
            value="{{ old('total_floors', $building?->total_floors ?? 1) }}"
            required min="1"
            class="mt-1.5 {{ $inputClass }}" />
        @error('total_floors')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="{{ $labelClass }}">
            Deskripsi
            <span class="ml-1 text-muted-foreground font-normal">(opsional)</span>
        </label>
        <textarea name="description" id="description" rows="3"
            placeholder="Keterangan peruntukan dan kondisi gedung…"
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors resize-none"
        >{{ old('description', $building?->description) }}</textarea>
        @error('description')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div class="flex items-center gap-2.5 pt-1">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
            @checked(old('is_active', $building?->is_active ?? true))
            class="h-4 w-4 rounded border-border text-primary focus:ring-ring" />
        <label for="is_active" class="text-xs font-medium text-foreground cursor-pointer select-none">
            Gedung aktif dan dapat digunakan
        </label>
    </div>
</div>
