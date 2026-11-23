<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">Buat akun</h1>
            <p class="mt-1 text-sm text-muted-foreground">Daftar untuk mulai melaporkan fasilitas.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input id="name" class="mt-1.5 w-full" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Nama Anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 w-full" type="email" name="email"
                    :value="old('email')" required autocomplete="username"
                    placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="mt-1.5 w-full" type="password" name="password"
                    required autocomplete="new-password" placeholder="Min. 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                <x-text-input id="password_confirmation" class="mt-1.5 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>

            <x-primary-button class="mt-1 w-full">
                Buat Akun
            </x-primary-button>
        </form>
    </div>

    <p class="mt-4 text-center text-xs text-muted-foreground">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Masuk di sini</a>
    </p>
</x-guest-layout>
