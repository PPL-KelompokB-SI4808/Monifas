@props(['locations'])
<form method="GET" action="{{ route('facility.buildings.index') }}" class="flex flex-wrap gap-4 items-center justify-between bg-white dark:bg-zinc-800 p-4 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
    <div class="flex flex-wrap gap-2 items-center flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode gedung..." class="px-3 py-2 border rounded-md text-sm border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        
        <select name="location_id" class="px-3 py-2 border rounded-md text-sm border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Lokasi</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
            @endforeach
        </select>

        <select name="is_active" class="px-3 py-2 border rounded-md text-sm border-zinc-300 dark:border-zinc-600 bg-transparent text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Filter</button>
        <a href="{{ route('facility.buildings.index') }}" class="px-4 py-2 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-md text-sm font-medium">Reset</a>
    </div>
</form>