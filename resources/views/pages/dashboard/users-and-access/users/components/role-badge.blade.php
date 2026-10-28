@props([
    'role' => null,
])

@php
    $slug = match (true) {
        $role instanceof \App\Models\Role => $role->slug,
        $role instanceof \App\Enums\UserRole => $role->value,
        is_string($role) => $role,
        default => null,
    };

    $name = match (true) {
        $role instanceof \App\Models\Role => $role->name,
        $role instanceof \App\Enums\UserRole => $role->label(),
        is_string($role) => ucwords(str_replace(['_', '-'], ' ', $role)),
        default => 'Tanpa Peran',
    };

    $badgeClasses = match ($slug) {
        'admin' => 'bg-primary/10 text-primary border-primary/20',
        'facility_user' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        'technician' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'supervisor' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
        'management' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        default => 'bg-secondary text-secondary-foreground border-border',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium border ' . $badgeClasses]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70"></span>
    {{ $name }}
</span>
