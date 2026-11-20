<x-layouts.dashboard title="Detail Ulasan #{{ $feedback->id }}">
    @php
        $user = auth()->user();
        $isOwner = (int) $feedback->user_id === (int) $user?->id;
        $isStaff = $user?->hasRole(\App\Enums\UserRole::Admin) || $user?->hasRole(\App\Enums\UserRole::Supervisor);
        $rating = (int) $feedback->rating;
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Link -->
        <div class="flex items-center justify-between">
            <a
                href="{{ route('supporting.feedback.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground hover:text-foreground"
            >
                <x-lucide-arrow-left class="h-3.5 w-3.5" />
                Kembali ke Daftar Umpan Balik
            </a>

            @if($isOwner && ! $feedback->isLocked())
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('supporting.feedback.edit', $feedback) }}"
                        class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium border border-border bg-background text-foreground hover:bg-secondary transition-colors"
                    >
                        <x-lucide-edit class="h-3 w-3" />
                        Edit
                    </a>
                    <form
                        method="POST"
                        action="{{ route('supporting.feedback.destroy', $feedback) }}"
                        onsubmit="return confirm('Yakin ingin menghapus ulasan ini?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-xs font-medium border border-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-500/10 transition-colors"
                        >
                            <x-lucide-trash-2 class="h-3 w-3" />
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Flash messages -->
        @if (session('success'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                <x-lucide-check-circle-2 class="h-4 w-4 shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-xs font-medium text-red-600 dark:text-red-400">
                <x-lucide-alert-circle class="h-4 w-4 shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Card Detail -->
        <x-ui.card class="p-6 space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
                <div>
                    <div class="flex items-center gap-1.5">
                        @for($i = 1; $i <= 5; $i++)
                            <x-lucide-star
                                class="h-5 w-5 {{ $i <= $rating ? 'text-amber-400 fill-amber-400' : 'text-muted-foreground/30 fill-transparent' }}"
                            />
                        @endfor
                        <span class="ml-1 text-sm font-bold text-foreground">({{ $rating }} dari 5 Bintang)</span>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Dikirim oleh <span class="font-medium text-foreground">{{ $feedback->user?->name ?? 'Pelapor' }}</span>
                        pada {{ $feedback->created_at?->translatedFormat('l, d F Y - H:i') }}
                    </p>
                </div>

                <div>
                    @if($feedback->isLocked())
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                            <x-lucide-check-circle-2 class="h-3.5 w-3.5" />
                            Terkunci (Sudah Ditanggapi)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-600 dark:text-amber-400">
                            <x-lucide-clock class="h-3.5 w-3.5" />
                            Menunggu Tanggapan Resmi
                        </span>
                    @endif
                </div>
            </div>

            <!-- Report and Maintenance Meta Info -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 rounded-lg bg-muted/40 p-4 border border-border text-xs">
                <div>
                    <span class="text-muted-foreground block mb-0.5">Nomor Tiket Laporan:</span>
                    <a href="{{ route('reporting.reports.show', $feedback->report_id) }}" class="font-semibold text-primary hover:underline text-sm">
                        #{{ $feedback->report?->ticket_number ?? $feedback->report_id }}
                    </a>
                </div>
                <div>
                    <span class="text-muted-foreground block mb-0.5">Judul Laporan:</span>
                    <span class="font-medium text-foreground">{{ $feedback->report?->title ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-muted-foreground block mb-0.5">Aset / Fasilitas:</span>
                    <span class="font-medium text-foreground">{{ $feedback->report?->facility?->name ?? 'Fasilitas Umum' }}</span>
                </div>
                <div>
                    <span class="text-muted-foreground block mb-0.5">Kategori Kerusakan:</span>
                    <span class="font-medium text-foreground">{{ $feedback->report?->damageCategory?->name ?? '-' }}</span>
                </div>
                @if($feedback->repair?->officer?->user)
                    <div class="sm:col-span-2 border-t border-border/60 pt-2 mt-1">
                        <span class="text-muted-foreground">Teknisi Pelaksana:</span>
                        <span class="font-medium text-foreground ml-1">{{ $feedback->repair->officer->user->name }}</span>
                    </div>
                @endif
            </div>

            <!-- Reporter's Comment -->
            <div>
                <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Ulasan / Masukan Pelapor</h4>
                <div class="rounded-lg border border-border bg-background p-4 text-xs text-foreground leading-relaxed whitespace-pre-line">
                    {{ $feedback->comment }}
                </div>
            </div>

            <!-- Admin Official Response -->
            @if($feedback->admin_response)
                <div class="rounded-xl border border-primary/20 bg-primary/5 p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-primary/10 pb-2">
                        <div class="flex items-center gap-2 text-primary font-semibold text-xs">
                            <x-lucide-shield-check class="h-4 w-4" />
                            Tanggapan Resmi Pihak Pengelola
                        </div>
                        <span class="text-2xs text-muted-foreground">
                            {{ $feedback->responded_at?->translatedFormat('d F Y, H:i') }}
                        </span>
                    </div>

                    <p class="text-xs text-foreground leading-relaxed whitespace-pre-line">
                        {{ $feedback->admin_response }}
                    </p>

                    <div class="text-2xs text-muted-foreground pt-1">
                        Oleh: <span class="font-medium text-foreground">{{ $feedback->responder?->name ?? 'Administrator' }}</span>
                    </div>
                </div>
            @elseif($isStaff)
                <!-- Form Tanggapi Langsung untuk Admin / Supervisor -->
                <div class="rounded-xl border border-border bg-card p-5 space-y-3">
                    <h4 class="text-xs font-semibold text-foreground flex items-center gap-1.5">
                        <x-lucide-reply class="h-4 w-4 text-primary" />
                        Beri Tanggapan Resmi
                    </h4>
                    <form method="POST" action="{{ route('supporting.feedback.respond', $feedback) }}" class="space-y-3">
                        @csrf
                        <textarea
                            name="admin_response"
                            rows="3"
                            required
                            placeholder="Tulis tanggapan atau evaluasi penanganan sarana fasilitas..."
                            class="w-full rounded-md border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        ></textarea>
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-md px-4 py-1.5 text-xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
                            >
                                <x-lucide-send class="h-3.5 w-3.5" />
                                Kirim Tanggapan
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.dashboard>
