<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-lg font-bold tracking-tight text-foreground">Lupa Password?</h1>
        <p class="text-xs text-muted-foreground mt-1">
            Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full">
                {{ __('Kirim Link Reset Password') }}
            </x-primary-button>
        </div>

        <div class="text-center pt-3 border-t border-border mt-5">
            <a href="{{ route('login') }}" class="text-xs text-primary font-medium hover:underline">
                &larr; Kembali ke halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
