<x-guest-layout>
    <div class="rounded-xl border border-border bg-background p-8 shadow-sm">
        {{-- Header --}}
        <div class="mb-7">
            <h1 class="text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">Verifikasi email</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Kami mengirimkan tautan verifikasi ke email Anda. Cek kotak masuk dan klik tautannya.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="mb-5 rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-3 text-xs font-medium text-emerald-600">
                Tautan verifikasi baru telah dikirim ke email Anda.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full">
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit"
                class="text-xs text-muted-foreground transition-colors hover:text-foreground">
                Keluar dari akun ini
            </button>
        </form>
    </div>
</x-guest-layout>
