@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground shadow-2xs placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/20 disabled:cursor-not-allowed disabled:opacity-50 transition-colors']) }}>
