<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-lg font-bold tracking-tight text-foreground">Masuk ke Akun</h1>
        <p class="text-xs text-muted-foreground mt-1">Masukkan email dan password untuk mengakses dashboard</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-muted-foreground hover:text-foreground transition-colors" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-border bg-background text-primary focus:ring-ring focus:ring-offset-0" name="remember">
                <span class="ms-2 text-xs text-muted-foreground">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <div class="text-center pt-3 border-t border-border mt-5">
                <p class="text-xs text-muted-foreground">
                    Belum memiliki akun?
                    <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>
