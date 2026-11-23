{{--
    Primary button — Design.md spec:
    - 6px radius
    - Medium height 38px
    - Hover: indigo glow shadow + 1px lift
    - Focus: 3px indigo rgba ring
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-[6px] h-[38px] px-4 text-sm font-semibold bg-primary text-primary-foreground transition-all hover:bg-primary-hover hover:shadow-[0_4px_12px_rgba(99,102,241,0.35)] hover:-translate-y-px active:translate-y-0 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-[rgba(99,102,241,0.12)] disabled:pointer-events-none disabled:opacity-50 cursor-pointer']) }}>
    {{ $slot }}
</button>
