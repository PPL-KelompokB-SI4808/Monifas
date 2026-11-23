<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">Atur ulang password</h1>
            <p class="mt-1 text-sm text-muted-foreground">Buat password baru untuk akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1.5 w-full" type="email" name="email"
                    :value="old('email', $request->email)" required autofocus
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password" value="Password Baru" />
                <x-text-input id="password" class="mt-1.5 w-full" type="password" name="password"
                    required autocomplete="new-password" placeholder="Min. 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                <x-text-input id="password_confirmation" class="mt-1.5 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>

            <x-primary-button class="mt-1 w-full">
                Simpan Password Baru
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
