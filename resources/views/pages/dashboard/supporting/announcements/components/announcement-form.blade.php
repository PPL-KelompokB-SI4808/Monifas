@props([
    'announcement' => null,
    'action',
    'method' => 'POST',
])

@php
    use App\Enums\AnnouncementStatus;
    use App\Enums\UserRole;

    $publishAtValue = old('publish_at', $announcement?->publish_at ? $announcement->publish_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $expiresAtValue = old('expires_at', $announcement?->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i'));
    $currentStatus = old('status', $announcement?->status instanceof AnnouncementStatus ? $announcement->status->value : ($announcement?->status ?? 'draft'));
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    <div class="rounded-xl border border-border bg-background p-6 shadow-2xs space-y-5">
        <h3 class="text-sm font-semibold text-foreground pb-3 border-b border-border flex items-center gap-2">
            <x-lucide-megaphone class="h-4 w-4 text-primary" />
            <span>{{ $announcement ? 'Perbarui Data Pengumuman' : 'Informasi Pengumuman Baru' }}</span>
        </h3>

        <!-- Title -->
        <div>
            <label for="title" class="block text-xs font-medium text-foreground">
                Judul Pengumuman <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', $announcement?->title) }}"
                required
                maxlength="200"
                placeholder="Contoh: Pemadaman Listrik Berkala di Gedung Utama"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            @error('title')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Category -->
            <div>
                <label for="category" class="block text-xs font-medium text-foreground">
                    Kategori Pengumuman <span class="text-red-500">*</span>
                </label>
                <select
                    name="category"
                    id="category"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="" disabled {{ old('category', $announcement?->category) ? '' : 'selected' }}>Pilih Kategori</option>
                    <option value="Pemeliharaan Rutin" @selected(old('category', $announcement?->category) === 'Pemeliharaan Rutin')>Pemeliharaan Rutin</option>
                    <option value="Keadaan Darurat" @selected(old('category', $announcement?->category) === 'Keadaan Darurat')>Keadaan Darurat</option>
                    <option value="Informasi Umum" @selected(old('category', $announcement?->category) === 'Informasi Umum')>Informasi Umum</option>
                    <option value="Renovasi & Pembangunan" @selected(old('category', $announcement?->category) === 'Renovasi & Pembangunan')>Renovasi & Pembangunan</option>
                </select>
                @error('category')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Option -->
            <div>
                <label for="status" class="block text-xs font-medium text-foreground">
                    Status Awal / Pengaturan
                </label>
                <select
                    name="status"
                    id="status"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    @foreach (AnnouncementStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected($currentStatus === $statusOption->value)>
                            {{ $statusOption->label() }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-muted-foreground">Status non-draft otomatis disesuaikan dengan waktu tayang.</p>
                @error('status')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Content -->
        <div>
            <label for="content" class="block text-xs font-medium text-foreground">
                Isi Pengumuman <span class="text-red-500">*</span>
            </label>
            <textarea
                name="content"
                id="content"
                rows="5"
                required
                placeholder="Tuliskan detail informasi atau instruksi lengkap untuk pengguna..."
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >{{ old('content', $announcement?->content) }}</textarea>
            @error('content')
                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Target Role -->
            <div>
                <label for="target_role" class="block text-xs font-medium text-foreground">
                    Target Peran Pengguna
                </label>
                <select
                    name="target_role"
                    id="target_role"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    <option value="">Semua Pengguna (Global)</option>
                    @foreach (UserRole::cases() as $roleOption)
                        <option value="{{ $roleOption->value }}" @selected(old('target_role', $announcement?->target_role) === $roleOption->value)>
                            {{ $roleOption->label() }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-muted-foreground">Kosongkan jika ditujukan untuk seluruh penghuni fasilitas.</p>
                @error('target_role')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Building ID -->
            <div>
                <label for="target_building_id" class="block text-xs font-medium text-foreground">
                    Target Gedung Spesifik (ID Gedung)
                </label>
                <input
                    type="number"
                    name="target_building_id"
                    id="target_building_id"
                    value="{{ old('target_building_id', $announcement?->target_building_id) }}"
                    placeholder="Kosongkan untuk semua gedung"
                    min="1"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                <p class="mt-1 text-[11px] text-muted-foreground">Kosongkan jika berlaku umum untuk seluruh lokasi gedung.</p>
                @error('target_building_id')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Schedule / Date Range -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2 border-t border-border">
            <!-- Publish At -->
            <div>
                <label for="publish_at" class="block text-xs font-medium text-foreground">
                    Waktu Tayang (Publish At) <span class="text-red-500">*</span>
                </label>
                <input
                    type="datetime-local"
                    name="publish_at"
                    id="publish_at"
                    value="{{ $publishAtValue }}"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                <p class="mt-1 text-[11px] text-muted-foreground">Jika diatur ke masa depan, pengumuman akan otomatis terjadwal.</p>
                @error('publish_at')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expires At -->
            <div>
                <label for="expires_at" class="block text-xs font-medium text-foreground">
                    Waktu Kedaluwarsa (Expires At) <span class="text-red-500">*</span>
                </label>
                <input
                    type="datetime-local"
                    name="expires_at"
                    id="expires_at"
                    value="{{ $expiresAtValue }}"
                    required
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                />
                <p class="mt-1 text-[11px] text-muted-foreground">Harus berada sesudah waktu tayang.</p>
                @error('expires_at')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3">
        <a
            href="{{ route('supporting.announcements.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2 text-xs font-medium text-foreground hover:bg-secondary transition-colors"
        >
            Batal
        </a>
        <button
            type="submit"
            class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow-2xs hover:bg-primary/90 transition-colors"
        >
            <x-lucide-check class="h-3.5 w-3.5" />
            <span>{{ $announcement ? 'Perbarui Pengumuman' : 'Simpan Pengumuman' }}</span>
        </button>
    </div>
</form>

