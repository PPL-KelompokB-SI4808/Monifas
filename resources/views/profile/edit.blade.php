<x-layouts.dashboard title="Profil Akun">
    <div class="mx-auto max-w-2xl space-y-5">

        {{-- Page Header --}}
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-muted-foreground">Akun</p>
            <h1 class="mt-1 text-xl font-bold tracking-tight text-foreground" style="letter-spacing: -0.02em;">
                Profil Akun
            </h1>
        </div>

        {{-- Profile Information --}}
        <div class="rounded-xl border border-border bg-background p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Password --}}
        <div class="rounded-xl border border-border bg-background p-6">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="rounded-xl border border-red-500/20 bg-red-500/5 p-6">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
</x-layouts.dashboard>
