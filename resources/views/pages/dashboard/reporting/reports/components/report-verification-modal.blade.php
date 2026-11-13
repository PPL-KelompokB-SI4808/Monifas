@props([
    'report',
    'priorities',
])

<div
    x-data="{
        open: false,
        action: 'verify',
        rejectionReason: '',
        priorityId: '{{ $report->priority_id }}'
    }"
    @open-verification-modal.window="open = true"
    @keydown.escape.window="open = false"
    class="relative z-50"
>
    <!-- Trigger Button (can be clicked on show page) -->
    <button
        type="button"
        @click="open = true"
        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
    >
        <x-lucide-shield-check class="h-4 w-4" />
        <span>Verifikasi Laporan</span>
    </button>

    <!-- Modal Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-background/80 backdrop-blur-xs"
        style="display: none;"
    ></div>

    <!-- Modal Dialog -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        style="display: none;"
    >
        <div
            @click.outside="open = false"
            class="w-full max-w-lg rounded-2xl border border-border bg-background p-6 shadow-xl space-y-5"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <x-lucide-clipboard-check class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-foreground">Verifikasi Laporan #{{ $report->ticket_number }}</h3>
                        <p class="text-[11px] text-muted-foreground">Tentukan persetujuan laporan dan validasi parameter SLA.</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                >
                    <x-lucide-x class="h-4 w-4" />
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('reporting.reports.verify', $report) }}" method="POST" class="space-y-4">
                @csrf

                <!-- Choice Action Tabs (Verify vs Reject) -->
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-foreground">Keputusan Supervisor <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Pilihan 1: Setujui (Verify) -->
                        <label
                            @click="action = 'verify'"
                            :class="action === 'verify' ? 'border-primary bg-primary/5 text-primary' : 'border-border bg-background text-muted-foreground hover:bg-secondary'"
                            class="flex items-center gap-2.5 rounded-xl border p-3 cursor-pointer transition-all"
                        >
                            <input
                                type="radio"
                                name="action"
                                value="verify"
                                x-model="action"
                                class="sr-only"
                            />
                            <div
                                :class="action === 'verify' ? 'bg-primary text-primary-foreground' : 'bg-secondary text-muted-foreground'"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                            >
                                <x-lucide-check class="h-4 w-4" />
                            </div>
                            <div class="text-left">
                                <span class="block text-xs font-bold">Setujui Laporan</span>
                                <span class="block text-[10px] text-muted-foreground">Status &rarr; Diverifikasi</span>
                            </div>
                        </label>

                        <!-- Pilihan 2: Tolak (Reject) -->
                        <label
                            @click="action = 'reject'"
                            :class="action === 'reject' ? 'border-red-500 bg-red-500/5 text-red-600 dark:text-red-400' : 'border-border bg-background text-muted-foreground hover:bg-secondary'"
                            class="flex items-center gap-2.5 rounded-xl border p-3 cursor-pointer transition-all"
                        >
                            <input
                                type="radio"
                                name="action"
                                value="reject"
                                x-model="action"
                                class="sr-only"
                            />
                            <div
                                :class="action === 'reject' ? 'bg-red-500 text-white' : 'bg-secondary text-muted-foreground'"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                            >
                                <x-lucide-x class="h-4 w-4" />
                            </div>
                            <div class="text-left">
                                <span class="block text-xs font-bold">Tolak Laporan</span>
                                <span class="block text-[10px] text-muted-foreground">Status &rarr; Ditolak</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Panel jika action = verify -->
                <div x-show="action === 'verify'" class="space-y-3 rounded-xl border border-primary/20 bg-primary/5 p-3.5">
                    <div class="flex items-start gap-2">
                        <x-lucide-info class="h-4 w-4 text-primary shrink-0 mt-0.5" />
                        <div class="text-[11px] text-foreground">
                            <p class="font-medium">Laporan akan diverifikasi dan siap ditugaskan ke petugas teknisi.</p>
                            <p class="text-muted-foreground mt-0.5">Anda dapat menyesuaikan prioritas SLA jika tingkat urgensi berbeda dengan estimasi pelapor.</p>
                        </div>
                    </div>

                    <!-- Dropdown Penyesuaian Prioritas SLA -->
                    <div class="space-y-1.5 pt-2">
                        <label for="modal_priority_id" class="text-xs font-semibold text-foreground">
                            Penyesuaian Prioritas SLA
                        </label>
                        <select
                            id="modal_priority_id"
                            name="priority_id"
                            x-model="priorityId"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground shadow-2xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition-colors"
                        >
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority->id }}">
                                    {{ $priority->name }} (Respon: {{ $priority->sla_response_hours }}j, Selesai: {{ $priority->sla_resolution_hours }}j)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Panel jika action = reject -->
                <div x-show="action === 'reject'" style="display: none;" class="space-y-3 rounded-xl border border-red-500/20 bg-red-500/5 p-3.5">
                    <div class="flex items-start gap-2">
                        <x-lucide-alert-triangle class="h-4 w-4 text-red-600 dark:text-red-400 shrink-0 mt-0.5" />
                        <div class="text-[11px] text-red-700 dark:text-red-300">
                            <p class="font-semibold">Laporan akan ditolak dan siklus penanganan ditutup.</p>
                            <p class="mt-0.5">Pelapor akan menerima notifikasi otomatis beserta alasan penolakan yang Anda berikan.</p>
                        </div>
                    </div>

                    <!-- Textarea Alasan Penolakan -->
                    <div class="space-y-1.5 pt-2">
                        <label for="modal_rejection_reason" class="text-xs font-semibold text-foreground flex items-center justify-between">
                            <span>Alasan Penolakan <span class="text-red-500">*</span></span>
                            <span class="text-[11px] font-normal text-muted-foreground">Wajib diisi</span>
                        </label>
                        <textarea
                            id="modal_rejection_reason"
                            name="rejection_reason"
                            rows="3"
                            x-model="rejectionReason"
                            :required="action === 'reject'"
                            placeholder="Tuliskan alasan penolakan laporan secara jelas (misal: laporan duplikat, informasi tidak valid, dsb)..."
                            class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground shadow-2xs focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors"
                        ></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border">
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-lg border border-border bg-background px-3.5 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        :class="action === 'reject' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-primary hover:bg-primary/90 text-primary-foreground'"
                        class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-xs font-bold shadow-2xs transition-colors cursor-pointer"
                    >
                        <x-lucide-check-circle-2 class="h-3.5 w-3.5" />
                        <span x-text="action === 'reject' ? 'Tolak Laporan' : 'Konfirmasi Verifikasi'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

