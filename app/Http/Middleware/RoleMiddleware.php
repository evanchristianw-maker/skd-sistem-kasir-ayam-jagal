<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memastikan pengguna memiliki salah satu role yang diizinkan.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user instanceof User) {
            abort(403, 'Akses ditolak.');
        }

        $knownRoles = [
            User::ROLE_ADMIN,
            User::ROLE_KASIR,
        ];

        if (
            !in_array($user->role, $knownRoles, true) ||
            !in_array($user->role, $roles, true)
        ) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
