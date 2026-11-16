@props(['assignments'])

<div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-border bg-secondary/40 font-medium text-muted-foreground">
                <tr>
                    <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">Laporan</th>
                    <th scope="col" class="px-3 py-3">Petugas</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Tenggat respons</th>
                    <th scope="col" class="px-3 py-3">Tenggat penyelesaian</th>
                    <th scope="col" class="px-3 py-3">Ditugaskan oleh</th>
                    <th scope="col" class="py-3 pl-3 pr-4 text-right sm:pr-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($assignments as $assignment)
                    <tr class="hover:bg-muted/30">
                        <td class="min-w-52 py-3.5 pl-4 pr-3 sm:pl-6">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-semibold text-foreground">{{ $assignment->report->title }}</span>
                                <span class="text-[11px] text-muted-foreground">{{ $assignment->report->ticket_number }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-3.5">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-medium text-foreground">{{ $assignment->officer->user?->name ?? 'Pengguna #' . $assignment->officer->user_id }}</span>
                                <span class="text-[11px] text-muted-foreground">{{ $assignment->officer->employee_code }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-3.5">
                            <x-assignments::assignment-status-badge :status="$assignment->status" />
                        </td>
                        <td class="whitespace-nowrap px-3 py-3.5 text-foreground">{{ $assignment->sla_response_due_at->format('d M Y, H:i') }}</td>
                        <td class="whitespace-nowrap px-3 py-3.5 text-foreground">{{ $assignment->sla_resolution_due_at->format('d M Y, H:i') }}</td>
                        <td class="whitespace-nowrap px-3 py-3.5 text-foreground">{{ $assignment->assignedBy?->name ?? '—' }}</td>
                        <td class="whitespace-nowrap py-3.5 pl-3 pr-4 text-right sm:pr-6">
                            <x-ui.button href="{{ route('maintenance.assignments.show', $assignment) }}" variant="outline" class="gap-1.5 px-3 py-1.5 text-xs">
                                <x-lucide-eye class="h-3.5 w-3.5" />
                                Detail
                            </x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="mx-auto flex max-w-xs flex-col items-center text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground">
                                    <x-lucide-clipboard-list class="h-6 w-6" />
                                </span>
                                <h2 class="mt-3 text-sm font-semibold text-foreground">Belum ada penugasan</h2>
                                <p class="mt-1 text-xs text-muted-foreground">Tidak ada penugasan yang cocok dengan filter saat ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($assignments->hasPages())
        <div class="border-t border-border px-4 py-3 sm:px-6">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
