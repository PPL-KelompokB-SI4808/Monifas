<x-layouts.dashboard title="Detail Kategori Kerusakan">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center gap-3">
            <a
                href="{{ route('location-and-category.damage-categories.index') }}"
                class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground hover:-translate-y-px"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali ke Daftar Kategori Kerusakan</span>
            </a>
        </div>

        {{-- Header Card --}}
        <div class="flex flex-col gap-4 rounded-xl border border-border bg-background p-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning/10 text-warning">
                    <x-lucide-alert-triangle class="h-6 w-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-foreground" style="letter-spacing: -0.03em;">{{ $category->name }}</h1>
                        @if($category->is_active)
                            <span class="inline-flex items-center gap-1 rounded-full border border-success/20 bg-success/10 px-2.5 py-0.5 text-[11px] font-medium text-success">
                                <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full border border-border bg-secondary px-2.5 py-0.5 text-[11px] font-medium text-muted-foreground">
                                <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-xs">
                        <span class="font-mono text-primary font-semibold">{{ $category->code }}</span>
                        @if($category->severity_hint)
                            <span class="inline-flex items-center gap-1 rounded-[4px] bg-secondary border border-border px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                                <x-lucide-activity class="h-3 w-3" />
                                <span>{{ $category->severity_hint }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2">
                <form action="{{ route('location-and-category.damage-categories.toggle-status', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-[6px] border border-border bg-background h-[38px] px-3 text-xs font-medium text-foreground transition-all hover:bg-secondary hover:-translate-y-px cursor-pointer"
                    >
                        @if($category->is_active)
                            <x-lucide-toggle-left class="h-3.5 w-3.5 text-muted-foreground" />
                            <span>Nonaktifkan</span>
                        @else
                            <x-lucide-toggle-right class="h-3.5 w-3.5 text-success" />
                            <span>Aktifkan</span>
                        @endif
                    </button>
                </form>

                <a
                    href="{{ route('location-and-category.damage-categories.edit', $category) }}"
                    class="inline-flex items-center gap-1.5 rounded-[6px] h-[38px] px-3 text-xs font-medium bg-primary text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                    <span>Edit</span>
                </a>

                <form
                    action="{{ route('location-and-category.damage-categories.destroy', $category) }}"
                    method="POST"
                    class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori kerusakan ini? (Proteksi FR-LCT-06: jika terikat dengan data laporan, akan dinonaktifkan secara aman via Soft Delete)');"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-[6px] border border-error/20 bg-background h-[38px] px-3 text-xs font-medium text-error transition-all hover:bg-error/10 hover:-translate-y-px cursor-pointer"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Detail Information Grid --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            {{-- Summary Info --}}
            <div class="rounded-xl border border-border bg-background p-5 space-y-4">
                <h2 class="text-xs font-semibold text-foreground border-b border-border pb-2">Informasi Klasifikasi</h2>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-muted-foreground block text-[11px]">Kode Kategori:</span>
                        <span class="font-mono font-medium text-foreground">{{ $category->code }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-[11px]">Indikasi Urgensi (Severity):</span>
                        <span class="font-medium text-foreground">{{ $category->severity_hint ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-[11px]">Dibuat Pada:</span>
                        <span class="font-medium text-foreground">{{ $category->created_at?->translatedFormat('d F Y H:i') ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-muted-foreground block text-[11px]">Terakhir Diperbarui:</span>
                        <span class="font-medium text-foreground">{{ $category->updated_at?->translatedFormat('d F Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Description & Rules --}}
            <div class="rounded-xl border border-border bg-background p-5 space-y-4 md:col-span-2">
                <h2 class="text-xs font-semibold text-foreground border-b border-border pb-2">Deskripsi & Indikator Penanganan</h2>
                <div class="text-xs leading-relaxed text-foreground">
                    @if($category->description)
                        <p class="whitespace-pre-line">{{ $category->description }}</p>
                    @else
                        <p class="text-muted-foreground italic">Tidak ada catatan gejala atau keterangan tambahan.</p>
                    @endif
                </div>

                <div class="mt-4 rounded-[8px] border border-warning/20 bg-warning/5 p-3 text-xs text-warning">
                    <p class="flex items-center gap-1.5 font-medium">
                        <x-lucide-shield-alert class="h-4 w-4 shrink-0" />
                        <span>Proteksi Data Pelaporan (FR-LCT-06)</span>
                    </p>
                    <p class="mt-1 text-[11px] text-muted-foreground">Kategori kerusakan yang pernah digunakan pada riwayat tiket laporan tidak akan dihapus dari basis data demi keutuhan audit kepatuhan SLA pemeliharaan, melainkan ditandai tidak aktif via mekanisme Soft Delete.</p>
                </div>
            </div>
        </div>

    </div>
</x-layouts.dashboard>
