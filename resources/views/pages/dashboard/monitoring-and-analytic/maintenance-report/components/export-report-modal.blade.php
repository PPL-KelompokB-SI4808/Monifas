@props([
    'filters' => [],
])

<div x-data="{ open: false }" class="relative">
    <x-ui.button type="button" variant="outline" size="sm" @click="open = true">
        <x-lucide-download class="h-4 w-4" />
        Ekspor / Cetak Laporan
    </x-ui.button>

    <div
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-foreground/40 p-4 backdrop-blur-xs"
    >
        <div
            @click.outside="open = false"
            class="w-full max-w-md rounded-xl border border-border bg-surface p-5 shadow-lg"
        >
            <div class="flex items-start justify-between border-b border-border pb-3">
                <div>
                    <h3 class="text-sm font-bold text-foreground">Ekspor Laporan Pemeliharaan</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        Pilih format dokumen berdasarkan filter aktif saat ini.
                    </p>
                </div>
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-md p-1 text-muted-foreground hover:bg-secondary hover:text-foreground cursor-pointer"
                    aria-label="Tutup modal"
                >
                    <x-lucide-x class="h-4 w-4" />
                </button>
            </div>

            <div class="mt-4 space-y-3">
                <a
                    href="{{ route('monitoring-and-analytic.maintenance-report.export', array_merge($filters, ['export' => 'csv'])) }}"
                    class="flex items-center justify-between rounded-lg border border-border bg-background p-3 text-xs transition hover:border-primary hover:bg-secondary/50"
                >
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-success/10 text-success">
                            <x-lucide-file-spreadsheet class="h-4 w-4" />
                        </div>
                        <div>
                            <p class="font-semibold text-foreground">Unduh CSV (Excel)</p>
                            <p class="text-muted-foreground">Format UTF-8 siap buka di Microsoft Excel</p>
                        </div>
                    </div>
                    <x-lucide-arrow-right class="h-4 w-4 text-muted-foreground" />
                </a>

                <a
                    href="{{ route('monitoring-and-analytic.maintenance-report.export', array_merge($filters, ['export' => 'print'])) }}"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center justify-between rounded-lg border border-border bg-background p-3 text-xs transition hover:border-primary hover:bg-secondary/50"
                >
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <x-lucide-printer class="h-4 w-4" />
                        </div>
                        <div>
                            <p class="font-semibold text-foreground">Tampilan Cetak / Simpan PDF</p>
                            <p class="text-muted-foreground">Buka halaman cetak siap simpan sebagai PDF</p>
                        </div>
                    </div>
                    <x-lucide-arrow-right class="h-4 w-4 text-muted-foreground" />
                </a>
            </div>

            <div class="mt-5 flex justify-end border-t border-border pt-3">
                <x-ui.button type="button" variant="ghost" size="sm" @click="open = false">
                    Tutup
                </x-ui.button>
            </div>
        </div>
    </div>
</div>
