<x-layouts.dashboard title="Tambah Perbaikan">
    <div class="mx-auto max-w-3xl space-y-5">
        {{-- Page Header --}}
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">Tambah Perbaikan</h1>
            <p class="mt-0.5 text-xs text-muted-foreground">Isi informasi pelaksanaan perbaikan fasilitas.</p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="flex items-start gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 shadow-2xs dark:text-red-400">
                <x-lucide-circle-x class="mt-0.5 size-4 shrink-0" />
                <div>
                    <p class="font-semibold">Terdapat kesalahan pada input:</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST"
              action="{{ route('repair.repairs.store') }}"
              class="rounded-xl border border-border bg-background p-6 shadow-2xs">
            @csrf

            @include('pages.dashboard.repair.repairs.partials.repair-execution-form')
        </form>
    </div>
</x-layouts.dashboard>
