<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-xs font-semibold text-white shadow-2xs hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 transition-colors cursor-pointer']) }}>
    {{ $slot }}
</button>
