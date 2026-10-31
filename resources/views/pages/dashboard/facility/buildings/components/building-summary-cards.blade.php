<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="p-4 bg-white dark:bg-zinc-800 shadow rounded-lg border border-zinc-200 dark:border-zinc-700">
        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Gedung</p>
        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ \App\Models\Building::count() }}</p>
    </div>
    <div class="p-4 bg-white dark:bg-zinc-800 shadow rounded-lg border border-zinc-200 dark:border-zinc-700">
        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Gedung Aktif</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ \App\Models\Building::where('is_active', true)->count() }}</p>
    </div>
    <div class="p-4 bg-white dark:bg-zinc-800 shadow rounded-lg border border-zinc-200 dark:border-zinc-700">
        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Lantai</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ \App\Models\Building::sum('total_floors') }}</p>
    </div>
</div>