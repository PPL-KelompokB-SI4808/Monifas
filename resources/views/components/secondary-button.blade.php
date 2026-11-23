{{--
    Secondary button — Design.md spec:
    - Transparent bg + 1px border
    - 6px radius, medium height 38px
    - Hover: bg-secondary + 1px lift
--}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-[6px] h-[38px] px-4 text-sm font-medium border border-border bg-transparent text-foreground transition-all hover:bg-secondary hover:-translate-y-px active:translate-y-0 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-[rgba(99,102,241,0.12)] disabled:pointer-events-none disabled:opacity-50 cursor-pointer']) }}>
    {{ $slot }}
</button>
