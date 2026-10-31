@props(['buildings'])
<div class="overflow-x-auto bg-white dark:bg-zinc-800 shadow rounded-lg border border-zinc-200 dark:border-zinc-700">
    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-left text-sm text-zinc-900 dark:text-zinc-100">
        <thead class="bg-zinc-50 dark:bg-zinc-900 text-xs uppercase text-zinc-500 dark:text-zinc-400">
            <tr>
                <th class="px-6 py-3">Kode</th>
                <th class="px-6 py-3">Nama Gedung</th>
                <th class="px-6 py-3">Lokasi</th>
                <th class="px-6 py-3">Total Lantai</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($buildings as $building)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $building->code }}</td>
                    <td class="px-6 py-4">{{ $building->name }}</td>
                    <td class="px-6 py-4">{{ $building->location->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $building->total_floors }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $building->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ $building->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('facility.buildings.show', $building) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Detail</a>
                        <a href="{{ route('facility.buildings.edit', $building) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ubah</a>
                        <form action="{{ route('facility.buildings.destroy', $building) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus/menonaktifkan gedung ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400">Belum ada data gedung.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">
        {{ $buildings->links() }}
    </div>
</div>