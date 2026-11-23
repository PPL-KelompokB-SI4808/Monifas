@php
    $locations        = \App\Models\Location::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    $damageCategories = \App\Models\DamageCategory::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
@endphp

<section id="laporan" class="border-b border-border bg-background">
    <div class="mx-auto max-w-6xl px-6 py-16 md:py-20">

        {{-- Section header --}}
        <div class="mb-10">
            <span class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Formulir laporan</span>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                Laporkan kerusakan fasilitas
            </h2>
            <p class="mt-1.5 text-sm text-muted-foreground">
                Tidak perlu akun. Isi form di bawah dan tim kami akan segera menindaklanjuti laporan Anda.
            </p>
        </div>

        {{-- Flash success --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <div class="rounded-xl border border-border bg-surface p-6 md:p-8"
             x-data="publicReportForm()"
             x-init="init()">

            <form
                method="POST"
                action="{{ route('reporting.public.store') }}"
                @submit.prevent="submitForm($el)"
                novalidate
            >
                @csrf

                {{-- ── BAGIAN 1: Identitas pelapor ─────────────────────────────── --}}
                <fieldset class="mb-8">
                    <legend class="mb-4 text-sm font-semibold uppercase tracking-widest text-muted-foreground">
                        Identitas Pelapor
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">

                        {{-- Nama --}}
                        <div>
                            <label for="reporter_name" class="mb-1.5 block text-sm font-medium text-foreground">
                                Nama Lengkap <span class="text-error">*</span>
                            </label>
                            <input
                                id="reporter_name"
                                name="reporter_name"
                                type="text"
                                maxlength="150"
                                placeholder="Nama Anda"
                                value="{{ old('reporter_name') }}"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('reporter_name') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                            @error('reporter_name')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div>
                            <label for="reporter_phone" class="mb-1.5 block text-sm font-medium text-foreground">
                                Nomor Telepon / WhatsApp <span class="text-error">*</span>
                            </label>
                            <input
                                id="reporter_phone"
                                name="reporter_phone"
                                type="tel"
                                maxlength="30"
                                placeholder="08xxxxxxxxxx"
                                value="{{ old('reporter_phone') }}"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('reporter_phone') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                            @error('reporter_phone')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </fieldset>

                {{-- ── BAGIAN 2: Lokasi kerusakan ──────────────────────────────── --}}
                <fieldset class="mb-8">
                    <legend class="mb-4 text-sm font-semibold uppercase tracking-widest text-muted-foreground">
                        Lokasi Kerusakan
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- Lokasi / wilayah --}}
                        <div>
                            <label for="location_id" class="mb-1.5 block text-sm font-medium text-foreground">
                                Wilayah / Area <span class="text-error">*</span>
                            </label>
                            <select
                                id="location_id"
                                name="location_id"
                                x-model="locationId"
                                @change="onLocationChange"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('location_id') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                                <option value="">— Pilih wilayah —</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Gedung --}}
                        <div>
                            <label for="building_id" class="mb-1.5 block text-sm font-medium text-foreground">
                                Gedung <span class="text-error">*</span>
                            </label>
                            <select
                                id="building_id"
                                name="building_id"
                                x-model="buildingId"
                                @change="onBuildingChange"
                                :disabled="buildings.length === 0"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-50 @error('building_id') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                                <option value="">— Pilih gedung —</option>
                                <template x-for="b in buildings" :key="b.id">
                                    <option :value="b.id" x-text="b.name"></option>
                                </template>
                            </select>
                            <template x-if="loadingBuildings">
                                <p class="mt-1 text-xs text-muted-foreground">Memuat gedung…</p>
                            </template>
                            @error('building_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ruangan (opsional) --}}
                        <div>
                            <label for="room_id" class="mb-1.5 block text-sm font-medium text-foreground">
                                Ruangan <span class="text-muted-foreground text-xs font-normal">(opsional)</span>
                            </label>
                            <select
                                id="room_id"
                                name="room_id"
                                x-model="roomId"
                                @change="onRoomChange"
                                :disabled="rooms.length === 0 && buildingId === ''"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-50 @error('room_id') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                                <option value="">— Pilih ruangan —</option>
                                <template x-for="r in rooms" :key="r.id">
                                    <option :value="r.id" x-text="r.name"></option>
                                </template>
                            </select>
                            <template x-if="loadingRooms">
                                <p class="mt-1 text-xs text-muted-foreground">Memuat ruangan…</p>
                            </template>
                            @error('room_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </fieldset>

                {{-- ── BAGIAN 3: Detail fasilitas & kerusakan ──────────────────── --}}
                <fieldset class="mb-8">
                    <legend class="mb-4 text-sm font-semibold uppercase tracking-widest text-muted-foreground">
                        Fasilitas &amp; Kerusakan
                    </legend>
                    <div class="grid gap-4 sm:grid-cols-2">

                        {{-- Fasilitas --}}
                        <div>
                            <label for="facility_id" class="mb-1.5 block text-sm font-medium text-foreground">
                                Fasilitas yang Rusak <span class="text-error">*</span>
                            </label>
                            <select
                                id="facility_id"
                                name="facility_id"
                                :disabled="facilities.length === 0 && buildingId === ''"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-50 @error('facility_id') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                                <option value="">— Pilih fasilitas —</option>
                                <template x-for="f in facilities" :key="f.id">
                                    <option :value="f.id" x-text="f.name"></option>
                                </template>
                            </select>
                            <template x-if="loadingFacilities">
                                <p class="mt-1 text-xs text-muted-foreground">Memuat fasilitas…</p>
                            </template>
                            @error('facility_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori kerusakan --}}
                        <div>
                            <label for="damage_category_id" class="mb-1.5 block text-sm font-medium text-foreground">
                                Kategori Kerusakan <span class="text-error">*</span>
                            </label>
                            <select
                                id="damage_category_id"
                                name="damage_category_id"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('damage_category_id') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                                <option value="">— Pilih kategori —</option>
                                @foreach ($damageCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('damage_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('damage_category_id')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </fieldset>

                {{-- ── BAGIAN 4: Deskripsi kerusakan ──────────────────────────── --}}
                <fieldset class="mb-8">
                    <legend class="mb-4 text-sm font-semibold uppercase tracking-widest text-muted-foreground">
                        Detail Laporan
                    </legend>
                    <div class="grid gap-4">

                        {{-- Judul --}}
                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-medium text-foreground">
                                Judul Laporan <span class="text-error">*</span>
                            </label>
                            <input
                                id="title"
                                name="title"
                                type="text"
                                maxlength="200"
                                placeholder="Contoh: Lampu koridor lantai 2 mati"
                                value="{{ old('title') }}"
                                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('title') border-error focus:border-error focus:ring-error/20 @enderror"
                            >
                            @error('title')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="description" class="mb-1.5 block text-sm font-medium text-foreground">
                                Deskripsi Kerusakan <span class="text-error">*</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                maxlength="2000"
                                placeholder="Jelaskan kerusakan secara detail: kapan terjadi, seberapa parah, dan apakah mengganggu aktivitas."
                                class="w-full resize-y rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('description') border-error focus:border-error focus:ring-error/20 @enderror"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </fieldset>

                {{-- Submit --}}
                <div class="flex items-center justify-between gap-4 border-t border-border pt-6">
                    <p class="text-xs text-muted-foreground">
                        Laporan akan langsung masuk ke sistem dan notifikasi dikirim ke tim kami.
                    </p>
                    <x-ui.button type="submit" x-bind:disabled="submitting" size="lg">
                        <template x-if="submitting">
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </template>
                        <template x-if="!submitting">
                            <span>Kirim Laporan</span>
                        </template>
                    </x-ui.button>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
function publicReportForm() {
    return {
        locationId:  '{{ old('location_id', '') }}',
        buildingId:  '{{ old('building_id', '') }}',
        roomId:      '{{ old('room_id', '') }}',

        buildings:  [],
        rooms:      [],
        facilities: [],

        loadingBuildings: false,
        loadingRooms:     false,
        loadingFacilities: false,

        submitting: false,

        init() {
            // Jika ada nilai lama (setelah validasi gagal), muat ulang dropdown bergantung
            if (this.locationId) {
                this.fetchBuildings(this.locationId).then(() => {
                    if (this.buildingId) {
                        this.fetchRooms(this.buildingId).then(() => {
                            this.fetchFacilities(this.buildingId, this.roomId);
                        });
                    }
                });
            }
        },

        async fetchBuildings(locationId) {
            if (!locationId) { this.buildings = []; return; }
            this.loadingBuildings = true;
            try {
                const res = await fetch(`{{ route('reporting.public.buildings') }}?location_id=${locationId}`);
                this.buildings = await res.json();
            } finally {
                this.loadingBuildings = false;
            }
        },

        async fetchRooms(buildingId) {
            if (!buildingId) { this.rooms = []; return; }
            this.loadingRooms = true;
            try {
                const res = await fetch(`{{ route('reporting.public.rooms') }}?building_id=${buildingId}`);
                this.rooms = await res.json();
            } finally {
                this.loadingRooms = false;
            }
        },

        async fetchFacilities(buildingId, roomId) {
            if (!buildingId) { this.facilities = []; return; }
            this.loadingFacilities = true;
            try {
                let url = `{{ route('reporting.public.facilities') }}?building_id=${buildingId}`;
                if (roomId) url += `&room_id=${roomId}`;
                const res = await fetch(url);
                this.facilities = await res.json();
            } finally {
                this.loadingFacilities = false;
            }
        },

        onLocationChange() {
            this.buildingId = '';
            this.roomId     = '';
            this.buildings  = [];
            this.rooms      = [];
            this.facilities = [];
            this.fetchBuildings(this.locationId);
        },

        onBuildingChange() {
            this.roomId     = '';
            this.rooms      = [];
            this.facilities = [];
            this.fetchRooms(this.buildingId);
            this.fetchFacilities(this.buildingId, '');
        },

        onRoomChange() {
            this.facilities = [];
            this.fetchFacilities(this.buildingId, this.roomId);
        },

        submitForm(form) {
            this.submitting = true;
            form.submit();
        },
    };
}
</script>
