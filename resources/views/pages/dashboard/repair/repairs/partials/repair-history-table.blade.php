<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold">No.</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Fasilitas</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Petugas</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal Mulai</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($repairs as $repair)
                <tr>
                    <td class="px-4 py-3 text-sm">
                        {{ $repairs->firstItem() + $loop->index }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        {{ $repair->facility->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        {{ $repair->officer->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        {{ $repair->started_at?->format('d-m-Y H:i') ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        {{ $repair->status?->value ?? $repair->status ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('repair.repairs.show', $repair) }}"
                           class="text-blue-600 hover:text-blue-800">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada data riwayat perbaikan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($repairs->hasPages())
    <div class="mt-4">
        {{ $repairs->links() }}
    </div>
    @endif