<x-layouts.dashboard title="Detail Penugasan">
    @php
        $canManageAssignment = ! in_array($assignment->status, [
            \App\Enums\AssignmentStatus::Completed,
            \App\Enums\AssignmentStatus::Cancelled,
            \App\Enums\AssignmentStatus::Reassigned,
        ], true);
    @endphp

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Detail Penugasan</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Tiket {{ $assignment->report->ticket_number }}</p>
            </div>
            <x-ui.button href="{{ route('maintenance.assignments.index') }}" variant="outline" class="w-full gap-2 sm:w-auto">
                <x-lucide-arrow-left class="h-4 w-4" />
                Kembali
            </x-ui.button>
        </div>

        <x-assignments::assignment-flash-messages />

        <x-ui.card class="space-y-6 p-5 sm:p-6">
            <div class="flex flex-col gap-3 border-b border-border pb-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Laporan</p>
                    <h2 class="mt-1 text-base font-semibold text-foreground">{{ $assignment->report->title }}</h2>
                    <p class="mt-1 text-xs text-primary">{{ $assignment->report->ticket_number }}</p>
                </div>
                <x-assignments::assignment-status-badge :status="$assignment->status" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Petugas</p>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $assignment->officer->user?->name ?? 'Pengguna #' . $assignment->officer->user_id }}</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">{{ $assignment->officer->employee_code }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Ditugaskan oleh</p>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $assignment->assignedBy?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Tenggat respons SLA</p>
                    <p class="mt-1 text-sm text-foreground">{{ $assignment->sla_response_due_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Tenggat penyelesaian SLA</p>
                    <p class="mt-1 text-sm text-foreground">{{ $assignment->sla_resolution_due_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="border-t border-border pt-4">
                <p class="text-xs font-medium text-muted-foreground">Catatan instruksi</p>
                <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-foreground">{{ $assignment->instruction_notes ?: 'Tidak ada catatan instruksi.' }}</p>
            </div>

            @if ($assignment->rejection_or_cancel_reason)
                <div class="rounded-lg border border-border bg-secondary/40 p-4">
                    <p class="text-xs font-medium text-muted-foreground">
                        {{ match ($assignment->status) {
                            \App\Enums\AssignmentStatus::Reassigned => 'Alasan pengalihan',
                            \App\Enums\AssignmentStatus::Cancelled => 'Alasan pembatalan',
                            \App\Enums\AssignmentStatus::RejectedByOfficer => 'Alasan penolakan',
                            default => 'Alasan',
                        } }}
                    </p>
                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-foreground">{{ $assignment->rejection_or_cancel_reason }}</p>
                </div>
            @endif
        </x-ui.card>

        @if ($canManageAssignment)
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <x-ui.button
                    type="button"
                    variant="outline"
                    class="w-full gap-2 sm:w-auto"
                    @click="$dispatch('open-modal', 'reassign-assignment')"
                >
                    <x-lucide-repeat-2 class="h-4 w-4" />
                    Alihkan
                </x-ui.button>
                <x-danger-button
                    type="button"
                    class="w-full gap-2 sm:w-auto"
                    @click="$dispatch('open-modal', 'cancel-assignment')"
                >
                    <x-lucide-x-circle class="h-4 w-4" />
                    Batalkan
                </x-danger-button>
            </div>

            <x-assignments::reassign-modal :assignment="$assignment" :officers="$officers" />
            <x-assignments::cancel-modal :assignment="$assignment" />
        @endif
    </div>
</x-layouts.dashboard>
