@props([
    'name' => 'rating',
    'value' => 5,
    'readonly' => false,
])

<div
    x-data="supportingFeedback({{ (int) $value }})"
    class="flex flex-col gap-2"
>
    <input type="hidden" name="{{ $name }}" :value="rating">

    <div class="flex items-center gap-1.5">
        <template x-for="i in 5" :key="i">
            <button
                type="button"
                @if(! $readonly)
                    @click="setRating(i)"
                    @mouseenter="setHover(i)"
                    @mouseleave="clearHover()"
                @endif
                :disabled="{{ $readonly ? 'true' : 'false' }}"
                class="p-1 rounded-md transition-transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-ring disabled:hover:scale-100"
                :title="i + ' Bintang'"
            >
                <svg
                    class="h-7 w-7 transition-colors"
                    :class="isActive(i) ? 'text-amber-400 fill-amber-400' : 'text-muted-foreground/30 fill-transparent'"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
            </button>
        </template>

        <span class="ml-2 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-border bg-muted/40 text-foreground" x-text="rating + ' / 5'"></span>
    </div>

    <p class="text-xs text-muted-foreground transition-opacity" x-text="ratingLabel"></p>
</div>
