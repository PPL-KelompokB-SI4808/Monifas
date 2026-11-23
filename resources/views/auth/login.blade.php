<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="font-display text-xl font-bold text-foreground" style="letter-spacing: -0.03em;">Masuk</h1>
            <p class="mt-1 text-sm text-muted-foreground">Lanjutkan ke dashboard MONIFAS.</p>
        </div>

        <x-auth-session-status class="mb-5" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 w-full" type="email" name="email"
                    :value="old('email')" required autofocus autocomplete="username"
                    placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Password" />
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-xs text-muted-foreground transition-colors hover:text-foreground">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <x-text-input id="password" class="mt-1.5 w-full" type="password" name="password"
                    required autocomplete="current-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div class="flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 rounded border-border text-primary focus:ring-ring focus:ring-offset-0">
                <label for="remember_me" class="text-xs text-muted-foreground cursor-pointer select-none">
                    Ingat saya
                </label>
            </div>

            <x-primary-button class="mt-1 w-full">
                Masuk
            </x-primary-button>
        </form>
    </div>

    @if (Route::has('register'))
        <p class="mt-4 text-center text-xs text-muted-foreground">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
