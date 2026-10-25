<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Batasi akses berdasarkan peran. Pemakaian: middleware('role:seller')
     * atau middleware('role:admin,seller').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda dinonaktifkan oleh admin.']);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Peran akun Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
