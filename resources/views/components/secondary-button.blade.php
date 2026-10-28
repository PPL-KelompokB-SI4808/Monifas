<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2.5 text-xs font-medium text-foreground shadow-2xs hover:bg-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 transition-colors cursor-pointer']) }}>
    {{ $slot }}
</button>
