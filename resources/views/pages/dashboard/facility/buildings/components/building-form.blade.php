@props(['building' => null, 'locations' => [], 'areaZones' => []])
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Lokasi</label>
        <select name="location_id" required class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Pilih Lokasi</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" {{ old('location_id', $building?->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
            @endforeach
        </select>
        @error('location_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Zona Area (Opsional)</label>
        <select name="area_zone_id" class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Pilih Zona Area</option>
            @foreach($areaZones as $zone)
                <option value="{{ $zone->id }}" {{ old('area_zone_id', $building?->area_zone_id) == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
            @endforeach
        </select>
        @error('area_zone_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Kode Gedung</label>
        <input type="text" name="code" value="{{ old('code', $building?->code) }}" required maxlength="30" class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('code') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Gedung</label>
        <input type="text" name="name" value="{{ old('name', $building?->name) }}" required maxlength="150" class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Total Lantai</label>
        <input type="number" name="total_floors" value="{{ old('total_floors', $building?->total_floors ?? 1) }}" required min="1" class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('total_floors') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</label>
        <textarea name="description" rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $building?->description) }}</textarea>
        @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>

    <div class="flex items-center space-x-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $building?->is_active ?? true) ? 'checked' : '' }} class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
        <label for="is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Gedung Aktif</label>
    </div>
</div>