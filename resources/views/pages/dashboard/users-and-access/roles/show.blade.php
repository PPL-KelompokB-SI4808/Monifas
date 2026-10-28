<x-layouts.dashboard title="Detail Peran">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $role->name }}</h1>
            <p class="text-xs text-muted-foreground mt-1">{{ $role->description }}</p>
        </div>
    </div>
</x-layouts.dashboard>
