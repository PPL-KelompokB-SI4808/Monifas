<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-amber-200/90 bg-amber-50/70 p-4 shadow-2xs">
    <div class="flex items-start gap-3">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
            <x-lucide-alert-triangle class="h-4 w-4" />
        </div>
        <div>
            <h4 class="text-xs font-bold text-amber-900 sm:text-sm">
                Perhatian: 4 Tiket Kritis Mendekati Batas SLA (&lt; 30 Menit)
            </h4>
            <p class="mt-0.5 text-xs text-amber-800/90 leading-relaxed">
                Tindakan segera diperlukan pada unit AC Ruang Server dan Lift Penumpang Gedung A agar target kepatuhan tidak terlewati.
            </p>
        </div>
    </div>

    <a
        href="#active-tickets-section"
        class="inline-flex items-center gap-1 text-xs font-bold text-amber-900 hover:text-amber-950 hover:underline shrink-0 self-start sm:self-auto"
    >
        Lihat Tiket Kritis
        <x-lucide-chevron-right class="h-3.5 w-3.5" />
    </a>
</div>
