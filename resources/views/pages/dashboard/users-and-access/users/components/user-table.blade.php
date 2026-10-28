@props([
    'users',
    'roles',
    'filters' => [],
])

<div class="space-y-4">
    <!-- Filter Toolbar -->
    <div class="rounded-xl border border-border bg-background p-4 shadow-2xs">
        <form method="GET" action="{{ route('users-and-access.users.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Search Keyword -->
                <div class="relative flex-1">
                    <x-lucide-search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari nama atau email pengguna..."
                        class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                </div>

                <!-- Role Filter -->
                <div class="w-full sm:w-48">
                    <select
                        name="role_id"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Peran</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(($filters['role_id'] ?? null) == $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-36">
                    <select
                        name="is_active"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua Status</option>
                        <option value="1" @selected(($filters['is_active'] ?? null) === true || ($filters['is_active'] ?? null) === '1')>Aktif</option>
                        <option value="0" @selected(($filters['is_active'] ?? null) === false || ($filters['is_active'] ?? null) === '0')>Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
                >
                    <x-lucide-filter class="h-3.5 w-3.5" />
                    <span>Filter</span>
                </button>

                @if(!empty($filters['search']) || !empty($filters['role_id']) || isset($filters['is_active']))
                    <a
                        href="{{ route('users-and-access.users.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                    >
                        <x-lucide-rotate-ccw class="h-3.5 w-3.5" />
                        <span>Reset</span>
                    </a>
                @endif

                <a
                    href="{{ route('users-and-access.users.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
                >
                    <x-lucide-user-plus class="h-3.5 w-3.5" />
                    <span>Tambah Pengguna</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table Container -->
    <div class="overflow-hidden rounded-xl border border-border bg-background shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-border bg-secondary/40 font-medium text-muted-foreground">
                    <tr>
                        <th scope="col" class="py-3 pl-4 pr-3 sm:pl-6">Pengguna</th>
                        <th scope="col" class="px-3 py-3">Peran Akses</th>
                        <th scope="col" class="px-3 py-3">Status</th>
                        <th scope="col" class="px-3 py-3">Terdaftar</th>
                        <th scope="col" class="py-3 pl-3 pr-4 text-right sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($users as $user)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="whitespace-nowrap py-3.5 pl-4 pr-3 sm:pl-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-bold text-xs border border-primary/20">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium text-foreground">{{ $user->name }}</span>
                                        <span class="text-[11px] text-muted-foreground">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5">
                                <x-users-and-access-users::role-badge :role="$user->role" />
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/10 px-2.5 py-0.5 text-[11px] font-medium text-red-600 dark:text-red-400 border border-red-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5 text-muted-foreground">
                                {{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap py-3.5 pl-3 pr-4 text-right text-xs sm:pr-6">
                                <div class="flex items-center justify-end gap-1.5" x-data="{ resetModalOpen: false }">
                                    <!-- Edit Link -->
                                    <a
                                        href="{{ route('users-and-access.users.edit', $user) }}"
                                        class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
                                        title="Ubah Data"
                                    >
                                        <x-lucide-pencil class="h-4 w-4" />
                                    </a>

                                    <!-- Toggle Active Status Form -->
                                    <form method="POST" action="{{ route('users-and-access.users.toggle-active', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="rounded-md p-1.5 {{ $user->is_active ? 'text-amber-500 hover:bg-amber-500/10' : 'text-emerald-600 hover:bg-emerald-500/10' }} transition-colors cursor-pointer"
                                            title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                        >
                                            @if($user->is_active)
                                                <x-lucide-user-x class="h-4 w-4" />
                                            @else
                                                <x-lucide-user-check class="h-4 w-4" />
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Reset Password Trigger -->
                                    <button
                                        type="button"
                                        @click="resetModalOpen = true"
                                        class="rounded-md p-1.5 text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors cursor-pointer"
                                        title="Reset Kata Sandi"
                                    >
                                        <x-lucide-key-round class="h-4 w-4" />
                                    </button>

                                    <!-- Delete Form -->
                                    <form
                                        method="POST"
                                        action="{{ route('users-and-access.users.destroy', $user) }}"
                                        class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-md p-1.5 text-red-500 hover:bg-red-500/10 transition-colors cursor-pointer"
                                            title="Hapus Pengguna"
                                        >
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    </form>

                                    <!-- Reset Password Modal -->
                                    <div
                                        x-show="resetModalOpen"
                                        x-cloak
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
                                        @click.self="resetModalOpen = false"
                                    >
                                        <div class="w-full max-w-sm rounded-xl border border-border bg-background p-6 shadow-xl text-left">
                                            <div class="flex items-center justify-between pb-3 border-b border-border">
                                                <h3 class="text-sm font-semibold text-foreground">Reset Kata Sandi</h3>
                                                <button type="button" @click="resetModalOpen = false" class="text-muted-foreground hover:text-foreground">
                                                    <x-lucide-x class="h-4 w-4" />
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('users-and-access.users.reset-password', $user) }}" class="mt-4 space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-medium text-foreground">Kata Sandi Baru</label>
                                                    <input
                                                        type="password"
                                                        name="password"
                                                        required
                                                        placeholder="Minimal 8 karakter"
                                                        class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                                                    />
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-foreground">Konfirmasi Kata Sandi</label>
                                                    <input
                                                        type="password"
                                                        name="password_confirmation"
                                                        required
                                                        placeholder="Ulangi kata sandi"
                                                        class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                                                    />
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button
                                                        type="button"
                                                        @click="resetModalOpen = false"
                                                        class="rounded-lg border border-border px-3 py-1.5 text-xs font-medium text-muted-foreground hover:bg-secondary"
                                                    >
                                                        Batal
                                                    </button>
                                                    <button
                                                        type="submit"
                                                        class="rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground hover:bg-primary/90 cursor-pointer"
                                                    >
                                                        Simpan Password
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="mx-auto flex max-w-xs flex-col items-center justify-center text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted/60 text-muted-foreground">
                                        <x-lucide-users class="h-6 w-6" />
                                    </div>
                                    <h4 class="mt-3 text-sm font-semibold text-foreground">Tidak ada pengguna ditemukan</h4>
                                    <p class="mt-1 text-xs text-muted-foreground">Coba ubah kriteria pencarian atau tambahkan pengguna baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-border px-4 py-3 sm:px-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
