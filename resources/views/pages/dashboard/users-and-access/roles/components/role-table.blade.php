@props([
    'roles',
])

<div class="space-y-4">
    <!-- Header Actions -->
    <div class="flex items-center justify-between rounded-xl border border-border bg-background p-4 shadow-2xs">
        <div>
            <span class="text-xs font-medium text-foreground">Total Peran Terdaftar:</span>
            <span class="ml-1 text-xs font-bold text-primary">{{ $roles->total() }}</span>
        </div>
        <a
            href="{{ route('users-and-access.roles.create') }}"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
        >
            <x-lucide-shield-plus class="h-3.5 w-3.5" />
            <span>Tambah Peran</span>
        </a>
    </div>

    <!-- Table Container -->
    <div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-border bg-secondary/40 font-medium text-muted-foreground">
                    <tr>
                        <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">Nama Peran</th>
                        <th scope="col" class="px-3 py-3">Identifier / Slug</th>
                        <th scope="col" class="px-3 py-3">Deskripsi</th>
                        <th scope="col" class="px-3 py-3">Pengguna Terkait</th>
                        <th scope="col" class="py-3 pl-3 pr-4 text-right sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="whitespace-nowrap py-3.5 pl-4 pr-3 sm:pl-6 font-semibold text-foreground">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10 text-primary border border-primary/20">
                                        <x-lucide-shield class="h-3.5 w-3.5" />
                                    </span>
                                    <span>{{ $role->name }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5 font-mono text-[11px] text-muted-foreground">
                                <code class="rounded bg-muted px-1.5 py-0.5">{{ $role->slug }}</code>
                            </td>
                            <td class="px-3 py-3.5 text-muted-foreground max-w-xs truncate">
                                {{ $role->description ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5">
                                <span class="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-0.5 text-[11px] font-medium text-secondary-foreground border border-border">
                                    <x-lucide-users class="h-3 w-3" />
                                    {{ $role->users_count ?? $role->users()->count() }} pengguna
                                </span>
                            </td>
                            <td class="whitespace-nowrap py-3.5 pl-3 pr-4 text-right text-xs sm:pr-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('users-and-access.roles.edit', $role) }}"
                                        class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                        title="Ubah Peran"
                                    >
                                        <x-lucide-pencil class="h-4 w-4" />
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('users-and-access.roles.destroy', $role) }}"
                                        class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus peran ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-md p-1.5 text-red-500 hover:bg-red-500/10 transition-colors cursor-pointer"
                                            title="Hapus Peran"
                                        >
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-muted-foreground">
                                Tidak ada data peran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($roles->hasPages())
            <div class="border-t border-border px-4 py-3 sm:px-6">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
