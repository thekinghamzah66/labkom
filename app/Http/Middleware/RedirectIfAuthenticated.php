<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class RedirectIfAuthenticated
{
    // SESUDAH ✅
public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            $user = Auth::guard($guard)->user();

            // ✅ Null safety: kalau role atau dashboard_route kosong, logout paksa
            if (!$user->role || !$user->role->dashboard_route) {
                Auth::guard($guard)->logout();
                return redirect()->route('welcome')
                    ->withErrors(['username' => 'Role akun tidak valid. Hubungi administrator.']);
            }

            // ✅ Cek route ada dulu sebelum redirect
            try {
                return redirect()->route($user->role->dashboard_route);
            } catch (\Exception $e) {
                Auth::guard($guard)->logout();
                return redirect()->route('welcome')
                    ->withErrors(['username' => 'Dashboard route tidak ditemukan.']);
            }
        }
    }

    return $next($request);
}
}
