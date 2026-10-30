<x-layouts.dashboard title="Tambah Perbaikan">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Tambah Perbaikan
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Isi informasi pelaksanaan perbaikan fasilitas.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">
                    Terdapat kesalahan pada input:
                </p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('repair.repairs.store') }}"
              class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
            @csrf

            @include('pages.dashboard.repair.repairs.partials.repair-execution-form')
        </form>
    </div>
</x-layouts.dashboard>
