<div class="space-y-4">
    <div>
        <label for="assignment_id" class="mb-1 block text-sm font-medium text-gray-700">
            ID Penugasan
        </label>
        <input type="number" name="assignment_id" id="assignment_id"
               value="{{ old('assignment_id', $repair->assignment_id ?? '') }}"
               required min="1"
               class="w-full rounded-md border-gray-300">
        @error('assignment_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="report_id" class="mb-1 block text-sm font-medium text-gray-700">
            ID Laporan (Opsional)
        </label>
        <input type="number" name="report_id" id="report_id"
               value="{{ old('report_id', $repair->report_id ?? '') }}"
               min="1"
               class="w-full rounded-md border-gray-300">
        @error('report_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="facility_id" class="mb-1 block text-sm font-medium text-gray-700">
            ID Fasilitas
        </label>
        <input type="number" name="facility_id" id="facility_id"
               value="{{ old('facility_id', $repair->facility_id ?? '') }}"
               required min="1"
               class="w-full rounded-md border-gray-300">
        @error('facility_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="officer_id" class="mb-1 block text-sm font-medium text-gray-700">
            ID Petugas
        </label>
        <input type="number" name="officer_id" id="officer_id"
               value="{{ old('officer_id', $repair->officer_id ?? '') }}"
               required min="1"
               class="w-full rounded-md border-gray-300">
        @error('officer_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="started_at" class="mb-1 block text-sm font-medium text-gray-700">
            Waktu Mulai
        </label>
        <input type="datetime-local" name="started_at" id="started_at"
               value="{{ old('started_at', isset($repair) && $repair->started_at ? $repair->started_at->format('Y-m-d\TH:i') : '') }}"
               required
               class="w-full rounded-md border-gray-300">
        @error('started_at')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="technical_action" class="mb-1 block text-sm font-medium text-gray-700">
            Tindakan Teknis
        </label>
        <textarea name="technical_action" id="technical_action" rows="4" required
                  class="w-full rounded-md border-gray-300">{{ old('technical_action', $repair->technical_action ?? '') }}</textarea>
        @error('technical_action')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="repair_result" class="mb-1 block text-sm font-medium text-gray-700">
            Hasil Perbaikan
        </label>
        <textarea name="repair_result" id="repair_result" rows="3"
                  class="w-full rounded-md border-gray-300">{{ old('repair_result', $repair->repair_result ?? '') }}</textarea>
        @error('repair_result')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="resulting_facility_condition" class="mb-1 block text-sm font-medium text-gray-700">
            Kondisi Fasilitas Setelah Perbaikan
        </label>
        <select name="resulting_facility_condition" id="resulting_facility_condition" required
                class="w-full rounded-md border-gray-300">
            @foreach (['good' => 'Baik', 'damaged' => 'Rusak', 'needs_follow_up' => 'Perlu Tindak Lanjut'] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('resulting_facility_condition', $repair->resulting_facility_condition ?? 'good') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('resulting_facility_condition')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Simpan
        </button>
        <a href="{{ route('repair.repairs.index') }}"
           class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">
            Batal
        </a>
    </div>
</div>