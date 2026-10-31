@props([
    'skills' => [],
])

<x-ui.card class="space-y-4 p-5 sm:p-6">
    <div>
        <h2 class="text-sm font-semibold text-foreground">Keterampilan</h2>
        <p class="mt-1 text-xs text-muted-foreground">Daftar keterampilan petugas.</p>
    </div>

    @forelse ($skills as $skill)
        <x-ui.badge>{{ $skill }}</x-ui.badge>
    @empty
        <div class="rounded-lg border border-dashed border-border px-4 py-6 text-center">
            <x-lucide-wrench class="mx-auto h-5 w-5 text-muted-foreground" />
            <p class="mt-2 text-xs font-medium text-foreground">Data keterampilan belum tersedia</p>
            <p class="mt-1 text-[11px] text-muted-foreground">Keterampilan akan ditampilkan setelah datanya tersedia.</p>
        </div>
    @endforelse
</x-ui.card>
