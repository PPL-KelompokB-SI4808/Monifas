@props([
    'address',
])

<div class="relative flex flex-col justify-between rounded-xl border border-border bg-card p-5 shadow-2xs hover:shadow-xs transition-shadow">
    <div class="space-y-3">
        <!-- Card Header: Label & Badges -->
        <div class="flex items-start justify-between gap-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-secondary px-2.5 py-1 text-xs font-semibold text-foreground">
                        <x-lucide-tag class="h-3.5 w-3.5 text-muted-foreground" />
                        {{ $address->label }}
                    </span>
                    @if ($address->is_primary)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <x-lucide-check-circle-2 class="h-3 w-3" />
                            Utama
                        </span>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex items-center gap-1.5 text-xs text-muted-foreground pt-1">
                    <x-lucide-user class="h-3.5 w-3.5" />
                    <span class="font-medium text-foreground">{{ $address->user?->name ?? 'Pengguna Tidak Diketahui' }}</span>
                    <span>&bull;</span>
                    <span>{{ $address->user?->email }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1 shrink-0">
                <a
                    href="{{ route('users-and-access.user-addresses.edit', $address) }}"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    title="Edit Alamat"
                >
                    <x-lucide-pencil class="h-3.5 w-3.5" />
                </a>
                <form
                    method="POST"
                    action="{{ route('users-and-access.user-addresses.destroy', $address) }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');"
                    class="inline-block"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 dark:border-red-900/30 bg-background text-red-500 hover:bg-red-500/10 hover:text-red-600 transition-colors"
                        title="Hapus Alamat"
                    >
                        <x-lucide-trash-2 class="h-3.5 w-3.5" />
                    </button>
                </form>
            </div>
        </div>

        <!-- Address Body -->
        <div class="rounded-lg bg-secondary/40 p-3 text-xs space-y-1.5">
            @if ($address->building_or_unit)
                <div class="flex items-center gap-1.5 font-medium text-foreground">
                    <x-lucide-building-2 class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                    <span>{{ $address->building_or_unit }}</span>
                </div>
            @endif

            <div class="flex items-start gap-1.5 text-muted-foreground">
                <x-lucide-map-pin class="h-3.5 w-3.5 mt-0.5 text-muted-foreground shrink-0" />
                <span class="leading-relaxed">{{ $address->address_detail }}</span>
            </div>
        </div>
    </div>

    <!-- Card Footer -->
    <div class="mt-4 pt-3 border-t border-border flex flex-wrap items-center justify-between gap-2 text-xs text-muted-foreground">
        <div class="flex items-center gap-3">
            @if ($address->contact_phone)
                <span class="inline-flex items-center gap-1">
                    <x-lucide-phone class="h-3.5 w-3.5" />
                    {{ $address->contact_phone }}
                </span>
            @endif
            @if ($address->postal_code)
                <span class="inline-flex items-center gap-1">
                    <x-lucide-mail class="h-3.5 w-3.5" />
                    Kode Pos: {{ $address->postal_code }}
                </span>
            @endif
        </div>
        <div class="text-[11px]">
            Diperbarui {{ $address->updated_at?->diffForHumans() }}
        </div>
    </div>
</div>
