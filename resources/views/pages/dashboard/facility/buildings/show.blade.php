<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6 bg-white dark:bg-zinc-800 p-6 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between border-b pb-4 border-zinc-200 dark:border-zinc-700">
            <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Detail Gedung: {{ $building->name }}</h1>
            <a href="{{ route('facility.buildings.index') }}" class="px-3 py-1.5 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-md text-sm font-medium">Kembali</a>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm text-zinc-700 dark:text-zinc-300">
            <div>
                <p class="font-medium text-zinc-500">Kode Gedung</p>
                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $building->code }}</p>
            </div>
            <div>
                <p class="font-medium text-zinc-500">Nama Gedung</p>
                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $building->name }}</p>
            </div>
            <div>
                <p class="font-medium text-zinc-500">Lokasi</p>
                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $building->location->name ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium text-zinc-500">Zona Area</p>
                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $building->areaZone->name ?? '-' }}</p>
            </div>
            <div>
                <p class="font-medium text-zinc-500">Total Lantai</p>
                <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $building->total_floors }}</p>
            </div>
            <div>
                <p class="font-medium text-zinc-500">Status</p>
                <p class="text-base font-semibold">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $building->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $building->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </p>
            </div>
            <div class="col-span-2">
                <p class="font-medium text-zinc-500">Deskripsi</p>
                <p class="text-base text-zinc-900 dark:text-zinc-100">{{ $building->description ?? '-' }}</p>
            </div>
        </div>
    </div>
</x-layouts.app>