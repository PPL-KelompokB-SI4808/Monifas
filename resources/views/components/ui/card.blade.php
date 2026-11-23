@props(['tag' => 'div', 'hover' => false])

{{--
    Card — Design.md spec:
    - White surface (#FFFFFF), 1px border, 12px radius
    - No shadow by default (flat)
    - hover prop: -2px lift + subtle shadow (0 8px 30px rgba(0,0,0,0.08))
    - Transition 200ms
--}}
<{{ $tag }} {{ $attributes->merge(['class' => 'rounded-xl border border-border bg-surface overflow-hidden transition-all duration-200' . ($hover ? ' hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)]' : '')]) }}>
    {{ $slot }}
</{{ $tag }}>
