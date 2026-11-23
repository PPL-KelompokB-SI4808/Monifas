<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">Konfirmasi password</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Area ini memerlukan verifikasi ulang. Masukkan password Anda untuk melanjutkan.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="mt-1.5 w-full" type="password" name="password"
                    required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <x-primary-button class="mt-1 w-full">
                Konfirmasi
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
