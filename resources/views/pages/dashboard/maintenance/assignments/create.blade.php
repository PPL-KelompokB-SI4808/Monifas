<x-layouts.dashboard title="Tugaskan Laporan">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Tugaskan Laporan</h1>
                <p class="mt-0.5 text-xs text-muted-foreground">Pilih petugas yang direkomendasikan untuk menangani laporan.</p>
            </div>
            <x-ui.button href="{{ route('maintenance.assignments.index') }}" variant="outline" class="w-full gap-2 sm:w-auto">
                <x-lucide-arrow-left class="h-4 w-4" />
                Kembali
            </x-ui.button>
        </div>

        <x-assignments::assignment-flash-messages />

        <x-ui.card class="space-y-4 p-5 sm:p-6">
            <div class="border-b border-border pb-3">
                <h2 class="text-sm font-semibold text-foreground">Ringkasan Laporan</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Nomor tiket</p>
                    <p class="mt-1 text-sm font-semibold text-primary">{{ $report->ticket_number }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-muted-foreground">Judul laporan</p>
                    <p class="mt-1 text-sm font-semibold text-foreground">{{ $report->title }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-muted-foreground">Deskripsi kerusakan</p>
                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-foreground">{{ $report->description }}</p>
                </div>
            </div>
        </x-ui.card>

        <form method="POST" action="{{ route('maintenance.assignments.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="report_id" value="{{ $report->id }}">

            <x-assignments::officer-recommendation-list :officers="$officers" />

            <x-ui.card class="space-y-2 p-5 sm:p-6">
                <label for="instruction_notes" class="block text-xs font-medium text-foreground">Catatan instruksi <span class="font-normal text-muted-foreground">(opsional)</span></label>
                <textarea
                    name="instruction_notes"
                    id="instruction_notes"
                    rows="4"
                    maxlength="1000"
                    placeholder="Tambahkan instruksi khusus untuk petugas, jika diperlukan"
                    @class([
                        'w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                        'border-border' => ! $errors->has('instruction_notes'),
                        'border-primary' => $errors->has('instruction_notes'),
                    ])
                >{{ old('instruction_notes') }}</textarea>
                <x-input-error :messages="$errors->get('instruction_notes')" />
            </x-ui.card>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-ui.button href="{{ route('maintenance.assignments.index') }}" variant="outline" class="w-full sm:w-auto">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" class="w-full gap-2 sm:w-auto" :disabled="$officers->isEmpty()">
                    <x-lucide-user-check class="h-4 w-4" />
                    Simpan Penugasan
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
