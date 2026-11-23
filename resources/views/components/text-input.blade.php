@props(['disabled' => false])

{{--
    Input — Design.md spec:
    - 6px radius
    - 10px vertical, 14px horizontal padding (py-2.5 px-3.5)
    - 14px font size
    - Focus: border indigo + 3px rgba ring
    - Error handled via parent x-input-error
--}}
<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'flex w-full rounded-[6px] border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground transition-colors focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-[rgba(99,102,241,0.12)] disabled:cursor-not-allowed disabled:opacity-50']) }}
>
