@props([
    'feedback',
])

@php
    $user = auth()->user();
    $isOwner = (int) $feedback->user_id === (int) $user?->id;
    $isStaff = $user?->hasRole(\App\Enums\UserRole::Admin) || $user?->hasRole(\App\Enums\UserRole::Supervisor);
    $rating = (int) $feedback->rating;
@endphp

<x-ui.card class="p-5 flex flex-col justify-between transition-shadow hover:shadow-md">
    <div class="space-y-4">
        <!-- Card Header: Rating, Ticket & Status -->
        <div class="flex items-start justify-between gap-2 border-b border-border/60 pb-3">
            <div>
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <x-lucide-star
                            class="h-4 w-4 {{ $i <= $rating ? 'text-amber-400 fill-amber-400' : 'text-muted-foreground/30 fill-transparent' }}"
                        />
                    @endfor
                    <span class="ml-1 text-xs font-semibold text-foreground">({{ $rating }}/5)</span>
                </div>
                <div class="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                    <span>Tiket: <a href="{{ route('reporting.reports.show', $feedback->report_id) }}" class="font-medium text-primary hover:underline">#{{ $feedback->report?->ticket_number ?? $feedback->report_id }}</a></span>
                    <span>•</span>
                    <span>{{ $feedback->created_at?->diffForHumans() }}</span>
                </div>
            </div>

            @if($feedback->isLocked())
                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 text-2xs font-semibold text-emerald-600 dark:text-emerald-400">
                    <x-lucide-check-circle-2 class="h-3 w-3" />
                    Ditanggapi
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-2xs font-semibold text-amber-600 dark:text-amber-400">
                    <x-lucide-clock class="h-3 w-3" />
                    Menunggu Tanggapan
                </span>
            @endif
        </div>

        <!-- Facility & Repair Info -->
        <div class="rounded-lg bg-muted/40 p-2.5 text-xs space-y-1">
            <div class="flex justify-between">
                <span class="text-muted-foreground">Fasilitas:</span>
                <span class="font-medium text-foreground">{{ $feedback->report?->facility?->name ?? 'Fasilitas Umum' }}</span>
            </div>
            @if($feedback->report?->facility?->category)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Kategori:</span>
                    <span class="text-foreground">{{ $feedback->report->facility->category->name }}</span>
                </div>
            @endif
            @if($feedback->repair?->officer?->user)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Teknisi:</span>
                    <span class="text-foreground font-medium">{{ $feedback->repair->officer->user->name }}</span>
                </div>
            @endif
        </div>

        <!-- Reporter Comment -->
        <div>
            <p class="text-xs font-medium text-muted-foreground mb-0.5">Ulasan Pelapor ({{ $feedback->user?->name ?? 'Pengguna' }}):</p>
            <p class="text-xs text-foreground bg-background rounded-md border border-border/80 p-2.5 whitespace-pre-line leading-relaxed">
                {{ $feedback->comment }}
            </p>
        </div>

        <!-- Admin Response (if exists) -->
        @if($feedback->admin_response)
            <div class="rounded-lg border border-primary/20 bg-primary/5 p-3 text-xs space-y-1">
                <div class="flex items-center justify-between text-primary font-medium">
                    <span class="flex items-center gap-1">
                        <x-lucide-message-square class="h-3.5 w-3.5" />
                        Tanggapan Pengelola ({{ $feedback->responder?->name ?? 'Admin' }})
                    </span>
                    <span class="text-2xs opacity-80">{{ $feedback->responded_at?->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <p class="text-foreground whitespace-pre-line leading-relaxed text-xs">
                    {{ $feedback->admin_response }}
                </p>
            </div>
        @endif
    </div>

    <!-- Actions Footer -->
    <div class="mt-4 flex items-center justify-between border-t border-border/60 pt-3 text-xs">
        <a
            href="{{ route('supporting.feedback.show', $feedback) }}"
            class="text-xs font-medium text-primary hover:underline inline-flex items-center gap-1"
        >
            Lihat Detail <x-lucide-chevron-right class="h-3 w-3" />
        </a>

        <div class="flex items-center gap-2">
            @if($isOwner && ! $feedback->isLocked())
                <a
                    href="{{ route('supporting.feedback.edit', $feedback) }}"
                    class="rounded px-2 py-1 text-2xs font-medium border border-border text-foreground hover:bg-secondary transition-colors"
                >
                    Edit
                </a>
                <form
                    method="POST"
                    action="{{ route('supporting.feedback.destroy', $feedback) }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');"
                    class="inline"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="rounded px-2 py-1 text-2xs font-medium border border-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-500/10 transition-colors"
                    >
                        Hapus
                    </button>
                </form>
            @endif

            @if($isStaff && ! $feedback->admin_response)
                <button
                    type="button"
                    @click="$dispatch('open-feedback-response-modal', { id: {{ $feedback->id }}, ticket: '{{ $feedback->report?->ticket_number }}' })"
                    class="rounded px-2.5 py-1 text-2xs font-medium bg-primary text-primary-foreground hover:bg-primary/90 transition-colors inline-flex items-center gap-1"
                >
                    <x-lucide-reply class="h-3 w-3" />
                    Tanggapi
                </button>
            @endif
        </div>
    </div>
</x-ui.card>
