<div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
    <table class="min-w-full divide-y divide-border">
        <thead class="bg-secondary/50">
            <tr>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">No.</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Fasilitas</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Petugas</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Tanggal Mulai</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-border">
            @forelse ($repairs as $repair)
                <tr class="transition-colors hover:bg-secondary/30">
                    <td class="px-4 py-3 text-xs text-muted-foreground">
                        {{ $repairs->firstItem() + $loop->index }}
                    </td>

                    <td class="px-4 py-3 text-xs font-medium text-foreground">
                        {{ $repair->facility->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-xs text-foreground">
                        {{ $repair->officer->name ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-xs text-foreground">
                        {{ $repair->started_at?->format('d-m-Y H:i') ?? '-' }}
                    </td>

                    <td class="px-4 py-3 text-xs">
                        @php $statusVal = $repair->status?->value ?? $repair->status ?? '-'; @endphp
                        <span class="inline-flex items-center rounded-md border border-border bg-secondary/50 px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                            {{ $statusVal }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-xs">
                        <a href="{{ route('repair.repairs.show', $repair) }}"
                           class="inline-flex items-center gap-1 text-xs font-medium text-primary transition-colors hover:text-primary/80">
                            <x-lucide-eye class="size-3.5" />
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center">
                        <div class="flex flex-col items-center gap-2 text-muted-foreground">
                            <x-lucide-wrench class="size-8 opacity-40" />
                            <p class="text-xs">Belum ada data riwayat perbaikan.</p>
                        </div>
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
