<x-layouts.dashboard title="Penugasan Pemeliharaan">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">Penugasan Pemeliharaan</h1>
            <p class="mt-0.5 text-xs text-muted-foreground">Pantau petugas dan tenggat setiap laporan yang ditugaskan.</p>
        </div>

        <x-assignments::assignment-flash-messages />

        <x-ui.card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('maintenance.assignments.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto_auto] lg:items-end">
                <div>
                    <label for="status" class="block text-xs font-medium text-foreground">Status penugasan</label>
                    <select
                        name="status"
                        id="status"
                        class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="officer_id" class="block text-xs font-medium text-foreground">Petugas</label>
                    <select
                        name="officer_id"
                        id="officer_id"
                        class="mt-1.5 w-full rounded-lg border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                        <option value="">Semua petugas</option>
                        @foreach ($officers as $officer)
                            <option value="{{ $officer->id }}" @selected((string) request('officer_id') === (string) $officer->id)>
                                {{ $officer->user?->name ?? 'Pengguna #' . $officer->user_id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-ui.button type="submit" class="w-full gap-2 sm:w-auto">
                    <x-lucide-filter class="h-4 w-4" />
                    Terapkan
                </x-ui.button>
                <x-ui.button href="{{ route('maintenance.assignments.index') }}" variant="outline" class="w-full sm:w-auto">
                    Reset
                </x-ui.button>
            </form>
        </x-ui.card>

        <x-assignments::assignable-report-list :reports="$assignableReports" />

        <x-assignments::assignment-table :assignments="$assignments" />
    </div>
</x-layouts.dashboard>
