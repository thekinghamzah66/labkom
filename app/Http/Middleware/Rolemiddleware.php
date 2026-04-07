<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware
 *
 * Middleware yang memproteksi route berdasarkan role user.
 * Mendukung single role: role:mahasiswa
 * Mendukung multiple roles: role:kalab,aslab
 *
 * Cara pendaftaran di bootstrap/app.php (Laravel 11/12):
 *
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias([
 *           'role' => \App\Http\Middleware\RoleMiddleware::class,
 *       ]);
 *   })
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')
                ->withErrors(['username' => 'Silakan login terlebih dahulu.']);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek apakah akun masih aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Validasi role: apakah role user ada dalam daftar roles yang diizinkan?
        if (!in_array($user->role->slug, $roles, true)) {
            // Jika user sudah login tapi mencoba akses URL role lain,
            // redirect ke dashboard mereka sendiri (bukan 403 yang membingungkan)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses ke halaman ini.',
                    'your_role' => $user->role->slug,
                ], 403);
            }

            return redirect()->route($user->role->dashboard_route)
                ->with('warning', "Anda tidak memiliki akses ke halaman tersebut. Diarahkan ke dashboard {$user->role->name} Anda.");
        }

        return $next($request);
    }
}