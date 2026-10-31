@props([
    'officer' => null,
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (in_array(strtoupper($method), ['PUT', 'PATCH']))
        @method($method)
    @endif

    <x-ui.card class="space-y-5 p-5 sm:p-6">
        <div class="border-b border-border pb-3">
            <h2 class="text-sm font-semibold text-foreground">
                {{ $officer ? 'Informasi Petugas' : 'Informasi Petugas Baru' }}
            </h2>
            <p class="mt-1 text-xs text-muted-foreground">Isi data sesuai informasi yang tersedia pada sistem.</p>
        </div>

        <div>
            <label for="user_id" class="block text-xs font-medium text-foreground">
                ID Pengguna <span class="text-primary">*</span>
            </label>
            <input
                type="number"
                name="user_id"
                id="user_id"
                value="{{ old('user_id', $officer?->user_id) }}"
                min="1"
                required
                inputmode="numeric"
                placeholder="Masukkan ID pengguna terdaftar"
                aria-describedby="user_id_help"
                @class([
                    'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                    'border-border' => ! $errors->has('user_id'),
                    'border-primary' => $errors->has('user_id'),
                ])
            />
            <p id="user_id_help" class="mt-1.5 text-[11px] text-muted-foreground">Daftar pengguna tidak tersedia pada data halaman ini.</p>
            @error('user_id')
                <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="employee_code" class="block text-xs font-medium text-foreground">
                Kode Pegawai <span class="text-primary">*</span>
            </label>
            <input
                type="text"
                name="employee_code"
                id="employee_code"
                value="{{ old('employee_code', $officer?->employee_code) }}"
                maxlength="50"
                required
                autocomplete="off"
                placeholder="Masukkan kode pegawai"
                @class([
                    'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                    'border-border' => ! $errors->has('employee_code'),
                    'border-primary' => $errors->has('employee_code'),
                ])
            />
            @error('employee_code')
                <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="specialization_notes" class="block text-xs font-medium text-foreground">Catatan Spesialisasi</label>
            <textarea
                name="specialization_notes"
                id="specialization_notes"
                rows="4"
                placeholder="Catatan spesialisasi (opsional)"
                @class([
                    'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                    'border-border' => ! $errors->has('specialization_notes'),
                    'border-primary' => $errors->has('specialization_notes'),
                ])
            >{{ old('specialization_notes', $officer?->specialization_notes) }}</textarea>
            @error('specialization_notes')
                <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="availability_status" class="block text-xs font-medium text-foreground">Status Ketersediaan</label>
                <select
                    name="availability_status"
                    id="availability_status"
                    class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                >
                    @foreach (\App\Enums\OfficerAvailability::cases() as $availability)
                        <option
                            value="{{ $availability->value }}"
                            @selected(old('availability_status', $officer?->availability_status?->value ?? \App\Enums\OfficerAvailability::Available->value) === $availability->value)
                        >
                            {{ match ($availability) {
                                \App\Enums\OfficerAvailability::Available => 'Tersedia',
                                \App\Enums\OfficerAvailability::Busy => 'Sibuk',
                                \App\Enums\OfficerAvailability::Unavailable => 'Tidak tersedia',
                            } }}
                        </option>
                    @endforeach
                </select>
                @error('availability_status')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="max_active_tasks" class="block text-xs font-medium text-foreground">
                    Batas Tugas Aktif <span class="text-primary">*</span>
                </label>
                <input
                    type="number"
                    name="max_active_tasks"
                    id="max_active_tasks"
                    value="{{ old('max_active_tasks', $officer?->max_active_tasks ?? 5) }}"
                    min="1"
                    required
                    inputmode="numeric"
                    @class([
                        'mt-1.5 w-full rounded-lg border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring',
                        'border-border' => ! $errors->has('max_active_tasks'),
                        'border-primary' => $errors->has('max_active_tasks'),
                    ])
                />
                @error('max_active_tasks')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-start gap-3 border-t border-border pt-4">
            <input type="hidden" name="is_active" value="0" />
            <input
                type="checkbox"
                name="is_active"
                id="is_active"
                value="1"
                @checked((bool) old('is_active', $officer?->is_active ?? true))
                class="mt-0.5 h-4 w-4 rounded border-border bg-background text-primary focus:ring-ring focus:ring-offset-0"
            />
            <div>
                <label for="is_active" class="text-xs font-medium text-foreground">Petugas aktif</label>
                <p class="mt-0.5 text-[11px] text-muted-foreground">Tentukan apakah profil petugas berstatus aktif.</p>
                @error('is_active')
                    <p class="mt-1.5 text-xs text-primary">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </x-ui.card>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <x-ui.button href="{{ route('maintenance.officers.index') }}" variant="outline" class="w-full sm:w-auto">
            Batal
        </x-ui.button>
        <x-ui.button type="submit" class="w-full sm:w-auto">
            {{ $officer ? 'Simpan Perubahan' : 'Simpan Petugas' }}
        </x-ui.button>
    </div>
</form>
