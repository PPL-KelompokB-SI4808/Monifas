@props(['assignment', 'officers'])

<div
    x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
    x-on:open-modal.window="if ($event.detail === 'reassign-assignment') open = true"
    x-on:close-modal.window="if ($event.detail === 'reassign-assignment') open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div class="absolute inset-0 bg-gray-900/60" @click="open = false" aria-hidden="true"></div>

    <div
        class="relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-lg overflow-y-auto rounded-xl bg-white text-foreground shadow-xl dark:bg-gray-800"
        role="dialog"
        aria-modal="true"
        aria-labelledby="reassign-modal-title"
        @click.stop
        @keydown.escape.window="open = false"
    >
        <div class="space-y-5 p-5 sm:p-6">
            <div>
                <h2 id="reassign-modal-title" class="text-base font-semibold text-foreground">Alihkan Penugasan</h2>
                <p class="mt-1 text-xs text-muted-foreground">Pilih petugas baru dan jelaskan alasan pengalihan.</p>
            </div>

            <form method="POST" action="{{ route('maintenance.assignments.reassign', $assignment) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="reassign_officer_id" class="block text-xs font-medium text-foreground">Petugas baru</label>
                    <select
                        name="officer_id"
                        id="reassign_officer_id"
                        required
                        @disabled($officers->isEmpty())
                        @class([
                            'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50',
                            'border-border' => ! $errors->has('officer_id'),
                            'border-primary' => $errors->has('officer_id'),
                        ])
                    >
                        <option value="">Pilih petugas</option>
                        @foreach ($officers as $officer)
                            <option value="{{ $officer->id }}" @selected((string) old('officer_id') === (string) $officer->id)>
                                {{ $officer->user?->name ?? 'Pengguna #' . $officer->user_id }} · {{ $officer->active_tasks_count }} tugas aktif
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('officer_id')" class="mt-1.5" />
                    @if ($officers->isEmpty())
                        <p class="mt-1.5 text-[11px] text-muted-foreground">Tidak ada petugas lain yang cocok untuk laporan ini.</p>
                    @endif
                </div>

                <div>
                    <label for="reassign_reason" class="block text-xs font-medium text-foreground">Alasan pengalihan <span class="text-primary">*</span></label>
                    <textarea
                        name="reason"
                        id="reassign_reason"
                        rows="4"
                        required
                        maxlength="1000"
                        @class([
                            'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                            'border-border' => ! $errors->has('reason'),
                            'border-primary' => $errors->has('reason'),
                        ])
                    >{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-1.5" />
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <x-secondary-button type="button" @click="$dispatch('close-modal', 'reassign-assignment')">Tutup</x-secondary-button>
                    <x-ui.button type="submit" class="gap-2" :disabled="$officers->isEmpty()">
                        <x-lucide-repeat-2 class="h-4 w-4" />
                        Alihkan Penugasan
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
