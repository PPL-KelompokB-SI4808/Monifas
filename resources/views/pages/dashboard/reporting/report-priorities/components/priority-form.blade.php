@props(['priority' => null, 'levels' => [], 'badgeVariants' => []])

<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    <!-- SLA Rule Callout -->
    <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-4 text-xs text-blue-700 dark:text-blue-300 flex items-start gap-3">
        <x-lucide-info class="h-4 w-4 shrink-0 text-blue-500 mt-0.5" />
        <div class="space-y-1">
            <span class="font-semibold">Ketentuan Batasan SLA:</span>
            <p class="text-[11px] text-muted-foreground leading-relaxed">
                Target respon awal (Response SLA) harus diisi minimal 1 jam. Target penyelesaian perbaikan (Resolution SLA) wajib lebih besar atau sama dengan target respon awal. Urutan urgensi default akan dihitung otomatis: <strong class="text-foreground">Darurat (1) &rarr; Tinggi (2) &rarr; Sedang (3) &rarr; Rendah (4)</strong> jika nomor urut tidak ditentukan manual.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Nama Prioritas -->
        <div>
            <label for="name" class="block text-xs font-medium text-foreground">
                Nama Prioritas <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                required
                maxlength="100"
                value="{{ old('name', $priority?->name) }}"
                placeholder="Contoh: Darurat Kritis"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Nama deskriptif tingkat prioritas laporan.</p>
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tingkat Urgensi (Level Enum) -->
        <div>
            <label for="level" class="block text-xs font-medium text-foreground">
                Tingkat Urgensi (Level) <span class="text-red-500">*</span>
            </label>
            <select
                name="level"
                id="level"
                required
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option value="">Pilih Tingkat Level</option>
                @foreach($levels as $lvl)
                    @php
                        $val = $lvl instanceof \App\Enums\PriorityLevel ? $lvl->value : $lvl;
                        $lbl = $lvl instanceof \App\Enums\PriorityLevel ? $lvl->label() : ucfirst($lvl);
                        $currentVal = old('level', $priority?->level instanceof \App\Enums\PriorityLevel ? $priority?->level->value : $priority?->level);
                    @endphp
                    <option value="{{ $val }}" @selected($currentVal === $val)>
                        {{ $lbl }} ({{ $val }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-[11px] text-muted-foreground">Kategori level baku penentu alur penanganan laporan.</p>
            @error('level')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- SLA Section -->
    <div class="rounded-xl border border-border bg-secondary/20 p-4">
        <h3 class="text-xs font-semibold text-foreground flex items-center gap-1.5">
            <x-lucide-gauge class="h-4 w-4 text-primary" />
            <span>Target Waktu Service Level Agreement (SLA)</span>
        </h3>

        <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <!-- Target SLA Response (Jam) -->
            <div>
                <label for="sla_response_hours" class="block text-xs font-medium text-foreground">
                    Target Respon Awal (Jam) <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1.5">
                    <input
                        type="number"
                        name="sla_response_hours"
                        id="sla_response_hours"
                        required
                        min="1"
                        max="720"
                        value="{{ old('sla_response_hours', $priority?->sla_response_hours ?? 1) }}"
                        class="w-full rounded-lg border border-border bg-background pl-3 pr-12 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                    <span class="absolute inset-y-0 right-3 flex items-center text-[11px] font-medium text-muted-foreground">
                        Jam
                    </span>
                </div>
                <p class="mt-1 text-[11px] text-muted-foreground">Maksimal batas waktu konfirmasi laporan sejak diajukan.</p>
                @error('sla_response_hours')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target SLA Resolution (Jam) -->
            <div>
                <label for="sla_resolution_hours" class="block text-xs font-medium text-foreground">
                    Target Penyelesaian (Jam) <span class="text-red-500">*</span>
                </label>
                <div class="relative mt-1.5">
                    <input
                        type="number"
                        name="sla_resolution_hours"
                        id="sla_resolution_hours"
                        required
                        min="1"
                        max="720"
                        value="{{ old('sla_resolution_hours', $priority?->sla_resolution_hours ?? 4) }}"
                        class="w-full rounded-lg border border-border bg-background pl-3 pr-12 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    />
                    <span class="absolute inset-y-0 right-3 flex items-center text-[11px] font-medium text-muted-foreground">
                        Jam
                    </span>
                </div>
                <p class="mt-1 text-[11px] text-muted-foreground">Maksimal batas waktu pekerjaan tuntas (&ge; target respon).</p>
                @error('sla_resolution_hours')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- Varian Warna Badge -->
        <div>
            <label for="badge_variant" class="block text-xs font-medium text-foreground">
                Indikator Warna Badge <span class="text-red-500">*</span>
            </label>
            <select
                name="badge_variant"
                id="badge_variant"
                required
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            >
                @foreach($badgeVariants as $key => $label)
                    <option value="{{ $key }}" @selected(old('badge_variant', $priority?->badge_variant) === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-[11px] text-muted-foreground">Gaya warna penanda badge prioritas pada dashboard & tabel laporan.</p>
            @error('badge_variant')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Urutan Prioritas (Sort Order) -->
        <div>
            <label for="sort_order" class="block text-xs font-medium text-foreground">
                Urutan Urgensi (Sort Order) <span class="text-xs text-muted-foreground">(Opsional)</span>
            </label>
            <input
                type="number"
                name="sort_order"
                id="sort_order"
                min="1"
                max="255"
                value="{{ old('sort_order', $priority?->sort_order) }}"
                placeholder="Biarkan kosong untuk otomatisasi urutan"
                class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p class="mt-1 text-[11px] text-muted-foreground">Nilai terkecil diproses lebih dahulu (Darurat: 1, Rendah: 4).</p>
            @error('sort_order')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Deskripsi -->
    <div>
        <label for="description" class="block text-xs font-medium text-foreground">
            Deskripsi / Panduan Penanganan <span class="text-xs text-muted-foreground">(Opsional)</span>
        </label>
        <textarea
            name="description"
            id="description"
            rows="3"
            placeholder="Jelaskan kriteria kerusakan yang tergolong dalam prioritas ini..."
            class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
        >{{ old('description', $priority?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Aktif -->
    <div class="rounded-lg border border-border bg-background p-3 flex items-start gap-2.5">
        <input
            type="checkbox"
            name="is_active"
            id="is_active"
            value="1"
            @checked(old('is_active', $priority ? $priority->is_active : true))
            class="h-4 w-4 rounded border-border text-primary focus:ring-primary mt-0.5"
        />
        <div>
            <label for="is_active" class="text-xs font-medium text-foreground cursor-pointer">
                Aktifkan Tingkat Prioritas Ini
            </label>
            <p class="text-[11px] text-muted-foreground">
                Jika dinonaktifkan, prioritas ini tidak akan muncul pada pilihan pembuatan laporan kerusakan baru.
            </p>
        </div>
    </div>
</div>

