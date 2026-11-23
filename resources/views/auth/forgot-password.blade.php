<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">Lupa password?</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Masukkan email Anda dan kami kirimkan tautan reset.
            </p>
        </div>

        <x-auth-session-status class="mb-5" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 w-full" type="email" name="email"
                    :value="old('email')" required autofocus placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <x-primary-button class="mt-1 w-full">
                Kirim Tautan Reset
            </x-primary-button>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-muted-foreground">
        <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">
            &larr; Kembali ke halaman masuk
        </a>
    </p>
</x-guest-layout>
