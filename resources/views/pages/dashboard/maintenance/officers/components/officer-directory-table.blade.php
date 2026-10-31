@props([
    'officers',
])

<div class="space-y-4">
    <x-ui.card class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-medium text-muted-foreground">Total petugas terdaftar</p>
            <p class="mt-1 text-lg font-bold text-foreground">{{ $officers->total() }}</p>
        </div>
        <x-ui.button href="{{ route('maintenance.officers.create') }}" class="w-full gap-2 sm:w-auto">
            <x-lucide-user-plus class="h-4 w-4" />
            Tambah Petugas
        </x-ui.button>
    </x-ui.card>

    <div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-border bg-secondary/40 font-medium text-muted-foreground">
                    <tr>
                        <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">Petugas</th>
                        <th scope="col" class="px-3 py-3">Spesialisasi</th>
                        <th scope="col" class="px-3 py-3">Kapasitas</th>
                        <th scope="col" class="px-3 py-3">Ketersediaan</th>
                        <th scope="col" class="px-3 py-3">Status</th>
                        <th scope="col" class="py-3 pl-3 pr-4 text-right sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($officers as $officer)
                        @php
                            $availability = $officer->availability_status;
                        @endphp
                        <tr class="hover:bg-muted/30">
                            <td class="whitespace-nowrap py-3.5 pl-4 pr-3 sm:pl-6">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-semibold text-foreground">
                                        {{ $officer->user?->name ?? 'Pengguna #' . $officer->user_id }}
                                    </span>
                                    <span class="text-[11px] text-muted-foreground">
                                        {{ $officer->employee_code }}
                                        @if ($officer->user?->email)
                                            <span aria-hidden="true">·</span> {{ $officer->user->email }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="max-w-xs px-3 py-3.5 text-muted-foreground">
                                <span class="line-clamp-2">{{ $officer->specialization_notes ?: 'Belum diisi' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5 text-foreground">
                                {{ $officer->max_active_tasks }} tugas aktif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5">
                                <x-ui.badge @class([
                                    'border-primary/20 bg-primary/10 text-primary' => $availability?->value === 'available',
                                    'border-border bg-secondary text-secondary-foreground' => $availability?->value === 'busy',
                                    'border-border bg-muted text-muted-foreground' => $availability?->value === 'unavailable',
                                ])>
                                    <span @class([
                                        'h-1.5 w-1.5 rounded-full bg-primary' => $availability?->value === 'available',
                                        'h-1.5 w-1.5 rounded-full bg-secondary-foreground' => $availability?->value === 'busy',
                                        'h-1.5 w-1.5 rounded-full bg-muted-foreground' => $availability?->value === 'unavailable',
                                    ])></span>
                                    {{ match ($availability?->value) {
                                        'available' => 'Tersedia',
                                        'busy' => 'Sibuk',
                                        default => 'Tidak tersedia',
                                    } }}
                                </x-ui.badge>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5">
                                <x-ui.badge @class([
                                    'border-primary/20 bg-primary/10 text-primary' => $officer->is_active,
                                    'border-border bg-muted text-muted-foreground' => ! $officer->is_active,
                                ])>
                                    {{ $officer->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-ui.badge>
                            </td>
                            <td class="whitespace-nowrap py-3.5 pl-3 pr-4 text-right sm:pr-6">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($availability?->value !== 'busy')
                                        <form method="POST" action="{{ route('maintenance.officers.toggle-availability', $officer) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                                title="{{ $availability?->value === 'available' ? 'Tandai tidak tersedia' : 'Tandai tersedia' }}"
                                            >
                                                <x-lucide-refresh-cw class="h-4 w-4" />
                                                <span class="sr-only">{{ $availability?->value === 'available' ? 'Tandai tidak tersedia' : 'Tandai tersedia' }}</span>
                                            </button>
                                        </form>
                                    @endif
                                    <a
                                        href="{{ route('maintenance.officers.edit', $officer) }}"
                                        class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                        title="Edit petugas"
                                    >
                                        <x-lucide-pencil class="h-4 w-4" />
                                        <span class="sr-only">Edit petugas</span>
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('maintenance.officers.destroy', $officer) }}"
                                        onsubmit="return confirm('Hapus petugas ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
                                            title="Hapus petugas"
                                        >
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                            <span class="sr-only">Hapus petugas</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mx-auto flex max-w-xs flex-col items-center text-center">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground">
                                        <x-lucide-users class="h-6 w-6" />
                                    </span>
                                    <h2 class="mt-3 text-sm font-semibold text-foreground">Belum ada petugas</h2>
                                    <p class="mt-1 text-xs text-muted-foreground">Tambahkan petugas pemeliharaan untuk memulai direktori.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($officers->hasPages())
            <div class="border-t border-border px-4 py-3 sm:px-6">
                {{ $officers->links() }}
            </div>
        @endif
    </div>
</div>
