<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if (! $user->is_active) {
            abort(403, 'Akun Anda dinonaktifkan.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        // Administrator role has global authority
        if ($user->hasRole(UserRole::Admin)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            $enum = UserRole::tryFrom($role);
            if ($enum && $user->hasRole($enum)) {
                return $next($request);
            }

            if ($user->role?->slug === $role) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk tindakan ini.');
    }
}
