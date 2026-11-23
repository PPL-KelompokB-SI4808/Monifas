@props(['variant' => 'default'])

{{--
    Badge / Chip — Design.md spec:
    - rounded-full (pill), 4px vertical 12px horizontal padding, 12px font
    - Variants: default (gray), primary (indigo), success (green), warning (yellow), error (red)
--}}
@php
    $classes = match ($variant) {
        'primary' => 'bg-primary/10 text-primary border-primary/20',
        'success' => 'bg-success/10 text-success border-success/20',
        'warning' => 'bg-warning/10 text-warning border-warning/20',
        'error'   => 'bg-error/10 text-error border-error/20',
        default   => 'bg-secondary text-muted-foreground border-border',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium ' . $classes]) }}>
    {{ $slot }}
</span>
