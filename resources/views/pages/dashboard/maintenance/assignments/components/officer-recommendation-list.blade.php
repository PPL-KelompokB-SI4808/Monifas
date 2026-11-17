@props(['officers'])

<x-ui.card class="space-y-4 p-5 sm:p-6">
    <div>
        <h2 class="text-sm font-semibold text-foreground">Petugas rekomendasi</h2>
        <p class="mt-1 text-xs text-muted-foreground">Pilih satu petugas yang tersedia dan sesuai dengan laporan.</p>
    </div>

    @forelse ($officers as $officer)
        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-border bg-background p-4 transition-colors hover:bg-muted/30">
            <input
                type="radio"
                name="officer_id"
                value="{{ $officer->id }}"
                required
                @checked((string) old('officer_id') === (string) $officer->id)
                class="mt-0.5 h-4 w-4 border-border text-primary focus:ring-ring"
            >
            <span class="min-w-0 flex-1">
                <span class="block text-xs font-semibold text-foreground">{{ $officer->user?->name ?? 'Pengguna #' . $officer->user_id }}</span>
                @if ($officer->specialization_notes)
                    <span class="mt-1 block text-xs text-muted-foreground">{{ $officer->specialization_notes }}</span>
                @endif
                <span class="mt-1 block text-[11px] text-muted-foreground">Beban tugas aktif: {{ $officer->active_tasks_count }}</span>
            </span>
        </label>
    @empty
        <div class="rounded-lg border border-dashed border-border px-4 py-6 text-center">
            <x-lucide-user-round-x class="mx-auto h-5 w-5 text-muted-foreground" />
            <p class="mt-2 text-xs font-medium text-foreground">Tidak ada petugas yang cocok</p>
            <p class="mt-1 text-[11px] text-muted-foreground">Belum ada petugas tersedia dengan keahlian dan cakupan lokasi yang sesuai.</p>
        </div>
    @endforelse

    <x-input-error :messages="$errors->get('officer_id')" />
</x-ui.card>
