<section>
    <div class="flex items-center gap-2 border-b border-border pb-4">
        <x-lucide-lock class="h-4 w-4 text-primary" />
        <h2 class="text-sm font-semibold text-foreground">Ubah Password</h2>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Password Saat Ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1.5 w-full"
                autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Password Baru" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1.5 w-full"
                autocomplete="new-password"
                placeholder="Min. 8 karakter" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1.5 w-full"
                autocomplete="new-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-3 pt-1">
            <x-primary-button>Perbarui Password</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-medium text-emerald-600">
                    Password diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
