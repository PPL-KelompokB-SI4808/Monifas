<form method="GET" action="{{ route('repair.repairs.index') }}"
      class="mb-6 grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-white p-4 md:grid-cols-4">

    <div>
        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
            Status Perbaikan
        </label>
        <select name="status" id="status"
                class="w-full rounded-md border-gray-300 text-sm">
            <option value="">Semua Status</option>
            @foreach (\App\Enums\RepairExecutionStatus::cases() as $status)
                <option value="{{ $status->value }}"
                    @selected(request('status') === $status->value)>
                    {{ $status->value }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="started_from" class="mb-1 block text-sm font-medium text-gray-700">
            Tanggal Mulai Dari
        </label>
        <input type="date" name="started_from" id="started_from"
               value="{{ request('started_from') }}"
               class="w-full rounded-md border-gray-300 text-sm">
    </div>

    <div>
        <label for="started_until" class="mb-1 block text-sm font-medium text-gray-700">
            Tanggal Mulai Sampai
        </label>
        <input type="date" name="started_until" id="started_until"
               value="{{ request('started_until') }}"
               class="w-full rounded-md border-gray-300 text-sm">
    </div>

    <div class="flex items-end gap-2">
        <button type="submit"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            Filter
        </button>
        <a href="{{ route('repair.repairs.index') }}"
           class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
            Reset
        </a>
    </div>
</form>