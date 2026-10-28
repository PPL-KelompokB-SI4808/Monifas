<x-layouts.dashboard title="Detail Pengguna">
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $user->name }}</h1>
            <p class="text-xs text-muted-foreground mt-1">{{ $user->email }}</p>
        </div>
    </div>
</x-layouts.dashboard>
