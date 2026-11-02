<form method="GET" action="{{ route('repair.repairs.index') }}"
      class="grid grid-cols-1 gap-4 rounded-xl border border-border bg-background p-4 shadow-2xs md:grid-cols-4">

    <div>
        <label for="status" class="mb-1 block text-xs font-medium text-foreground">
            Status Perbaikan
        </label>
        <select name="status" id="status"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
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
        <label for="started_from" class="mb-1 block text-xs font-medium text-foreground">
            Tanggal Mulai Dari
        </label>
        <input type="date" name="started_from" id="started_from"
               value="{{ request('started_from') }}"
               class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
    </div>

    <div>
        <label for="started_until" class="mb-1 block text-xs font-medium text-foreground">
            Tanggal Mulai Sampai
        </label>
        <input type="date" name="started_until" id="started_until"
               value="{{ request('started_until') }}"
               class="w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring">
    </div>

    <div class="flex items-end gap-2">
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-medium text-primary-foreground shadow-2xs transition-colors hover:bg-primary/90">
            <x-lucide-filter class="size-3.5" />
            Filter
        </button>
        <a href="{{ route('repair.repairs.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground">
            <x-lucide-x class="size-3.5" />
            Reset
        </a>
    </div>
</form>