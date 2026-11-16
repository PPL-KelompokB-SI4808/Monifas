@props(['status'])

<x-ui.badge @class([
    'border-border bg-secondary text-secondary-foreground' => $status->badgeVariant() === 'secondary',
    'border-primary/20 bg-primary/10 text-primary' => in_array($status->badgeVariant(), ['info', 'primary', 'emerald', 'warning'], true),
    'border-border bg-muted text-muted-foreground' => $status->badgeVariant() === 'danger',
])>
    <span @class([
        'h-1.5 w-1.5 rounded-full bg-primary' => in_array($status->badgeVariant(), ['info', 'primary', 'emerald'], true),
        'h-1.5 w-1.5 rounded-full bg-muted-foreground' => in_array($status->badgeVariant(), ['secondary', 'danger', 'warning'], true),
    ])></span>
    {{ $status->label() }}
</x-ui.badge>
