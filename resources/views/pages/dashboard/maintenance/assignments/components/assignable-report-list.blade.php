@props(['reports'])

<x-ui.card class="space-y-4 p-4 sm:p-5">
    <div>
        <h2 class="text-sm font-semibold text-foreground">Laporan menunggu penugasan</h2>
        <p class="mt-1 text-xs text-muted-foreground">Laporan terverifikasi yang belum memiliki penugasan aktif.</p>
    </div>

    <div class="divide-y divide-border">
        @forelse ($reports as $report)
            <div class="flex flex-col gap-3 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 space-y-1">
                    <p class="truncate text-sm font-medium text-foreground">{{ $report->title }}</p>
                    <p class="text-xs text-muted-foreground">{{ $report->facility?->name ?? 'Fasilitas tidak tersedia' }}</p>
                    <p class="text-xs text-muted-foreground">
                        Prioritas: {{ $report->priority?->name ?? '—' }}
                        <span aria-hidden="true">·</span>
                        Dilaporkan {{ $report->created_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <x-ui.button href="{{ route('maintenance.assignments.create', $report) }}" class="w-full gap-2 sm:w-auto">
                    <x-lucide-user-check class="h-4 w-4" />
                    Tugaskan
                </x-ui.button>
            </div>
        @empty
            <p class="py-2 text-xs text-muted-foreground">Belum ada laporan yang menunggu penugasan.</p>
        @endforelse
    </div>
</x-ui.card>
