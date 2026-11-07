<x-layouts.dashboard title="Detail Kategori Kerusakan">
    <div class="space-y-6">
        <!-- Breadcrumb / Back Link -->
        <div class="flex items-center gap-2 text-xs text-muted-foreground">
            <a href="{{ route('location-and-category.damage-categories.index') }}" class="hover:text-foreground inline-flex items-center gap-1 transition-colors">
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                <span>Kembali ke Daftar Kategori Kerusakan</span>
            </a>
        </div>

        <!-- Header Card -->
        <div class="flex flex-col gap-4 rounded-xl border border-border bg-card p-6 shadow-2xs sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <x-lucide-alert-triangle class="h-6 w-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $category->name }}</h1>
                        @if($category->is_active)
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span>Aktif</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground border border-border">
                                <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-xs">
                        <span class="font-mono text-primary font-semibold">{{ $category->code }}</span>
                        @if($category->severity_hint)
                            <span class="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium text-muted-foreground">
                                <x-lucide-activity class="h-3 w-3" />
                                <span>{{ $category->severity_hint }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <form action="{{ route('location-and-category.damage-categories.toggle-status', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors"
                    >
                        @if($category->is_active)
                            <x-lucide-toggle-left class="h-3.5 w-3.5 text-muted-foreground" />
                            <span>Nonaktifkan</span>
                        @else
                            <x-lucide-toggle-right class="h-3.5 w-3.5 text-emerald-500" />
                            <span>Aktifkan</span>
                        @endif
                    </button>
                </form>

                <a
                    href="{{ route('location-and-category.damage-categories.edit', $category) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
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
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-500/20 bg-background px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-500/10 transition-colors"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Detail Information Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <!-- Summary Info -->
            <div class="rounded-xl border border-border bg-card p-5 shadow-2xs space-y-4">
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

            <!-- Description & Rules -->
            <div class="rounded-xl border border-border bg-card p-5 shadow-2xs space-y-4 md:col-span-2">
                <h2 class="text-xs font-semibold text-foreground border-b border-border pb-2">Deskripsi & Indikator Penanganan</h2>
                <div class="text-xs leading-relaxed text-foreground">
                    @if($category->description)
                        <p class="whitespace-pre-line">{{ $category->description }}</p>
                    @else
                        <p class="text-muted-foreground italic">Tidak ada catatan gejala atau keterangan tambahan.</p>
                    @endif
                </div>

                <div class="mt-4 rounded-lg border border-amber-500/20 bg-amber-500/5 p-3 text-xs text-amber-600 dark:text-amber-400">
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
