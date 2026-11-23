<section>
    <div class="flex items-center gap-2 border-b border-border pb-4">
        <x-lucide-user class="h-4 w-4 text-primary" />
        <h2 class="text-sm font-semibold text-foreground">Informasi Profil</h2>
    </div>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1.5 w-full"
                :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1.5 w-full"
                :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-lg border border-yellow-500/20 bg-yellow-500/10 px-3 py-2 text-xs text-yellow-700">
                    Email belum diverifikasi.
                    <button form="send-verification"
                        class="ml-1 font-medium underline underline-offset-2 hover:no-underline focus:outline-none">
                        Kirim ulang tautan verifikasi.
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-xs font-medium text-emerald-600">
                        Tautan verifikasi baru telah dikirim ke email Anda.
                    </p>
                @endif
            @endif
        </div>

        <div class="flex items-center gap-3 pt-1">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-medium text-emerald-600">
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
