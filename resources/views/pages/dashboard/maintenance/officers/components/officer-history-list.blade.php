@props([
    'history' => [],
])

<x-ui.card class="space-y-4 p-5 sm:p-6">
    <div>
        <h2 class="text-sm font-semibold text-foreground">Riwayat Penugasan</h2>
        <p class="mt-1 text-xs text-muted-foreground">Catatan aktivitas penugasan petugas.</p>
    </div>

    @forelse ($history as $entry)
        <div class="border-l-2 border-border py-1 pl-3 text-xs text-foreground">
            {{ $entry }}
        </div>
    @empty
        <div class="rounded-lg border border-dashed border-border px-4 py-6 text-center">
            <x-lucide-clock-3 class="mx-auto h-5 w-5 text-muted-foreground" />
            <p class="mt-2 text-xs font-medium text-foreground">Riwayat penugasan belum tersedia</p>
            <p class="mt-1 text-[11px] text-muted-foreground">Data riwayat akan ditampilkan setelah data assignment tersedia.</p>
        </div>
    @endforelse
</x-ui.card>
