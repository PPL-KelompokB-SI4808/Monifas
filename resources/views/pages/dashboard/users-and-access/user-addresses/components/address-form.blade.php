@props([
    'address' => null,
    'users',
    'action',
    'method' => 'POST',
    'selectedUserId' => null,
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div class="rounded-xl border border-border bg-card p-6 shadow-2xs space-y-5">
        <h3 class="text-sm font-semibold text-foreground pb-3 border-b border-border">
            {{ $address ? 'Perbarui Informasi Alamat' : 'Informasi Alamat Pengguna' }}
        </h3>

        <!-- User Selection -->
        <div>
            <label for="user_id" class="block text-xs font-medium text-foreground">
                Pemilik Alamat (Pengguna) <span class="text-red-500">*</span>
            </label>
            <select
                name="user_id"
                id="user_id"
                required
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">-- Pilih Pengguna --</option>
                @foreach ($users as $user)
                    <option
                        value="{{ $user->id }}"
                        @selected((int) old('user_id', $address?->user_id ?? $selectedUserId) === (int) $user->id)
                    >
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Label and Building/Unit Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Label -->
            <div>
                <label for="label" class="block text-xs font-medium text-foreground">
                    Label Alamat <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="label"
                    id="label"
                    value="{{ old('label', $address?->label) }}"
                    required
                    placeholder="Contoh: Rumah, Kantor, Lab Komputer"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('label')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Building or Unit -->
            <div>
                <label for="building_or_unit" class="block text-xs font-medium text-foreground">
                    Gedung / Unit / Lantai
                </label>
                <input
                    type="text"
                    name="building_or_unit"
                    id="building_or_unit"
                    value="{{ old('building_or_unit', $address?->building_or_unit) }}"
                    placeholder="Contoh: Gedung Rektorat Lt. 3 / Unit 302"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('building_or_unit')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Address Detail -->
        <div>
            <label for="address_detail" class="block text-xs font-medium text-foreground">
                Detail Alamat Lengkap <span class="text-red-500">*</span>
            </label>
            <textarea
                name="address_detail"
                id="address_detail"
                rows="3"
                required
                placeholder="Tuliskan nama jalan, nomor bangunan, RT/RW, dan patokan lokasi..."
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >{{ old('address_detail', $address?->address_detail) }}</textarea>
            @error('address_detail')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contact Phone and Postal Code Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Contact Phone -->
            <div>
                <label for="contact_phone" class="block text-xs font-medium text-foreground">
                    Nomor Telepon Kontak
                </label>
                <input
                    type="text"
                    name="contact_phone"
                    id="contact_phone"
                    value="{{ old('contact_phone', $address?->contact_phone) }}"
                    placeholder="Contoh: 08123456789"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('contact_phone')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Postal Code -->
            <div>
                <label for="postal_code" class="block text-xs font-medium text-foreground">
                    Kode Pos
                </label>
                <input
                    type="text"
                    name="postal_code"
                    id="postal_code"
                    value="{{ old('postal_code', $address?->postal_code) }}"
                    placeholder="Contoh: 15412"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                @error('postal_code')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Is Primary Checkbox -->
        <div class="pt-2">
            <label class="relative flex items-start gap-3 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_primary"
                    id="is_primary"
                    value="1"
                    @checked(old('is_primary', $address?->is_primary))
                    class="mt-0.5 h-4 w-4 rounded border-border text-primary focus:ring-ring"
                />
                <div class="text-xs">
                    <span class="font-medium text-foreground">Jadikan sebagai Alamat Utama</span>
                    <p class="text-muted-foreground mt-0.5">
                        Jika dicentang, alamat ini akan menjadi alamat primer dan alamat primer lain milik pengguna ini akan otomatis dinonaktifkan status primernya.
                    </p>
                </div>
            </label>
            @error('is_primary')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('users-and-access.user-addresses.index') }}"
            class="rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-secondary hover:text-foreground transition-colors"
        >
            Batal
        </a>
        <button
            type="submit"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground hover:bg-primary/90 transition-colors shadow-2xs"
        >
            <x-lucide-save class="h-4 w-4" />
            <span>{{ $address ? 'Simpan Perubahan' : 'Tambah Alamat' }}</span>
        </button>
    </div>
</form>
