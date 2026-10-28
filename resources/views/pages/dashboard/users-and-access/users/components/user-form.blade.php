@props([
    'user' => null,
    'roles',
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-5">
        <h3 class="text-sm font-semibold text-foreground pb-3 border-b border-border">
            {{ $user ? 'Perbarui Informasi Pengguna' : 'Informasi Akun Pengguna' }}
        </h3>

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $user?->name) }}"
                required
                placeholder="Masukkan nama lengkap"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('name')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-medium text-foreground">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $user?->email) }}"
                required
                placeholder="nama@monifas.id"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('email')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <label for="role_id" class="block text-xs font-medium text-foreground">
                Peran Pengguna (Role)
            </label>
            <select
                name="role_id"
                id="role_id"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Pilih Peran Akses</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $user?->role_id) == $role->id)>
                        {{ $role->name }} — {{ $role->description }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="flex items-center gap-3 pt-2">
            <input
                type="hidden"
                name="is_active"
                value="0"
            />
            <input
                type="checkbox"
                name="is_active"
                id="is_active"
                value="1"
                @checked(old('is_active', $user?->is_active ?? true))
                class="h-4 w-4 rounded border-border bg-background text-primary focus:ring-ring focus:ring-offset-0 cursor-pointer"
            />
            <label for="is_active" class="text-xs font-medium text-foreground cursor-pointer">
                Akun Aktif (Dapat masuk ke dalam sistem)
            </label>
            @error('is_active')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password section -->
        <div class="pt-4 border-t border-border space-y-4">
            <div>
                <label for="password" class="block text-xs font-medium text-foreground">
                    Kata Sandi {{ $user ? '(Biarkan kosong jika tidak ingin mengubah)' : '*' }}
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    {{ $user ? '' : 'required' }}
                    placeholder="Minimal 8 karakter"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('password')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-medium text-foreground">
                    Konfirmasi Kata Sandi {{ $user ? '' : '*' }}
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    {{ $user ? '' : 'required' }}
                    placeholder="Ulangi kata sandi"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
            </div>
        </div>
    </div>

    <!-- Form Submit Actions -->
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('users-and-access.users.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
        >
            Batal
        </a>
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors cursor-pointer"
        >
            {{ $user ? 'Perbarui Pengguna' : 'Simpan Pengguna Baru' }}
        </button>
    </div>
</form>
