<x-layouts.dashboard title="Riwayat Perbaikan">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Riwayat Perbaikan
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Daftar pelaksanaan dan riwayat perbaikan fasilitas.
                </p>
            </div>

            <a href="{{ route('repair.repairs.create') }}"
               class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white">
                Tambah Perbaikan
            </a>
        </div>

        @include('pages.dashboard.repair.repairs.partials.repair-filter-bar')

        @include('pages.dashboard.repair.repairs.partials.repair-history-table')
    </div>
</x-layouts.dashboard>