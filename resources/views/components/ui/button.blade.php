@props([
    'type'    => 'button',
    'variant' => 'primary',
    'size'    => 'md',
    'href'    => null,
])

@php
    /*
     * Base: 6px radius (Design.md spec), font DM Sans via global css,
     * translate-y-[-1px] on hover per Design.md "All buttons shift up 1px on hover".
     */
    $base = 'inline-flex items-center justify-center gap-2 rounded-[6px] font-medium transition-all '
          . 'focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-[rgba(99,102,241,0.12)] '
          . 'disabled:pointer-events-none disabled:opacity-50 cursor-pointer hover:-translate-y-px active:translate-y-0';

    $variants = [
        'primary'     => 'bg-primary text-primary-foreground hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)]',
        'secondary'   => 'border border-border bg-transparent text-foreground hover:bg-secondary',
        'outline'     => 'border border-border bg-transparent text-foreground hover:bg-secondary',
        'ghost'       => 'bg-transparent text-foreground hover:bg-secondary',
        'destructive' => 'border border-error bg-transparent text-error hover:bg-error/5',
    ];

    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-[38px] px-4 text-sm',
        'lg' => 'h-11 px-6 text-sm',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
