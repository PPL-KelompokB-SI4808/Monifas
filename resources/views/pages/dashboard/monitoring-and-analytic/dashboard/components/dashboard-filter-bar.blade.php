@props([
    'filters' => [],
    'filterOptions' => [],
])

@php
    $locations = $filterOptions['locations'] ?? collect();
    $buildings = $filterOptions['buildings'] ?? collect();
    $facilities = $filterOptions['facilities'] ?? collect();
    $officers = $filterOptions['officers'] ?? collect();
    $statuses = $filterOptions['statuses'] ?? [];
@endphp

<x-ui.card class="p-4 sm:p-5">
    <div class="mb-4 flex flex-col gap-2 border-b border-border pb-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <x-lucide-filter class="h-4 w-4 text-primary" />
            <h2 class="text-xs font-bold tracking-wider text-foreground uppercase">
                Filter Parameter Operasional
            </h2>
        </div>
        <span class="text-xs text-muted-foreground">
            Sesuaikan rentang tanggal, lokasi, aset, status, dan teknisi
        </span>
    </div>

    <form method="GET" action="{{ route('monitoring-and-analytic.dashboard.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="space-y-1.5">
            <label for="filter-start-date" class="block text-xs font-medium text-muted-foreground">
                Tanggal Mulai
            </label>
            <input
                id="filter-start-date"
                type="date"
                name="start_date"
                value="{{ old('start_date', $filters['start_date'] ?? '') }}"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('start_date')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-end-date" class="block text-xs font-medium text-muted-foreground">
                Tanggal Akhir
            </label>
            <input
                id="filter-end-date"
                type="date"
                name="end_date"
                value="{{ old('end_date', $filters['end_date'] ?? '') }}"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('end_date')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-location" class="block text-xs font-medium text-muted-foreground">
                Lokasi / Kawasan
            </label>
            <select
                id="filter-location"
                name="location_id"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Semua Lokasi</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @selected((string) ($filters['location_id'] ?? '') === (string) $location->id)>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            @error('location_id')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-building" class="block text-xs font-medium text-muted-foreground">
                Gedung
            </label>
            <select
                id="filter-building"
                name="building_id"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Semua Gedung</option>
                @foreach ($buildings as $building)
                    <option value="{{ $building->id }}" @selected((string) ($filters['building_id'] ?? '') === (string) $building->id)>
                        {{ $building->name }}
                    </option>
                @endforeach
            </select>
            @error('building_id')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-facility" class="block text-xs font-medium text-muted-foreground">
                Fasilitas / Aset
            </label>
            <select
                id="filter-facility"
                name="facility_id"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Semua Fasilitas</option>
                @foreach ($facilities as $facility)
                    <option value="{{ $facility->id }}" @selected((string) ($filters['facility_id'] ?? '') === (string) $facility->id)>
                        {{ $facility->name }}
                    </option>
                @endforeach
            </select>
            @error('facility_id')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-status" class="block text-xs font-medium text-muted-foreground">
                Status Laporan
            </label>
            <select
                id="filter-status"
                name="status"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="filter-officer" class="block text-xs font-medium text-muted-foreground">
                Petugas Teknisi
            </label>
            <select
                id="filter-officer"
                name="officer_id"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Semua Petugas</option>
                @foreach ($officers as $officer)
                    <option value="{{ $officer->id }}" @selected((string) ($filters['officer_id'] ?? '') === (string) $officer->id)>
                        {{ $officer->user?->name ?? $officer->employee_code }}
                    </option>
                @endforeach
            </select>
            @error('officer_id')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-end gap-2">
            <x-ui.button type="submit" size="sm" class="flex-1">
                <x-lucide-search class="h-3.5 w-3.5" />
                Terapkan Filter
            </x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('monitoring-and-analytic.dashboard.index') }}">
                Reset
            </x-ui.button>
        </div>
    </form>
</x-ui.card>
