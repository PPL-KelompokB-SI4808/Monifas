@props(['assignment'])

<div
    x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
    x-on:open-modal.window="if ($event.detail === 'cancel-assignment') open = true"
    x-on:close-modal.window="if ($event.detail === 'cancel-assignment') open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div class="absolute inset-0 bg-gray-900/60" @click="open = false" aria-hidden="true"></div>

    <div
        class="relative z-10 max-h-[calc(100vh-2rem)] w-full max-w-lg overflow-y-auto rounded-xl bg-white text-foreground shadow-xl dark:bg-gray-800"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cancel-modal-title"
        @click.stop
        @keydown.escape.window="open = false"
    >
        <div class="space-y-5 p-5 sm:p-6">
            <div>
                <h2 id="cancel-modal-title" class="text-base font-semibold text-foreground">Batalkan Penugasan</h2>
                <p class="mt-1 text-xs text-muted-foreground">Tuliskan alasan pembatalan penugasan ini.</p>
            </div>

            <form method="POST" action="{{ route('maintenance.assignments.cancel', $assignment) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label for="cancel_reason" class="block text-xs font-medium text-foreground">Alasan pembatalan <span class="text-primary">*</span></label>
                    <textarea
                        name="reason"
                        id="cancel_reason"
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
                    <x-secondary-button type="button" @click="$dispatch('close-modal', 'cancel-assignment')">Tutup</x-secondary-button>
                    <x-danger-button type="submit" class="gap-2">
                        <x-lucide-x-circle class="h-4 w-4" />
                        Batalkan Penugasan
                    </x-danger-button>
                </div>
            </form>
        </div>
    </div>
</div>
