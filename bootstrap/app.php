<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // -----------------------------------------------------------------------
        // Alias Middleware — dipanggil via nama pendek di route
        // -----------------------------------------------------------------------
        $middleware->alias([
            // Proteksi role: digunakan sebagai role:mahasiswa, role:kalab, dll.
            'role'  => RoleMiddleware::class,

            // Redirect jika sudah login (untuk halaman guest seperti /login)
            'guest' => RedirectIfAuthenticated::class,
        ]);

        // -----------------------------------------------------------------------
        // Web Middleware Group — tambahan global untuk semua route web
        // -----------------------------------------------------------------------
        $middleware->web(append: [
            // Tambahkan middleware global web di sini jika diperlukan
        ]);

        // -----------------------------------------------------------------------
        // Enkripsi cookies yang TIDAK perlu dienkripsi (opsional)
        // -----------------------------------------------------------------------
        // $middleware->encryptCookies(except: ['nama_cookie']);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Tangani AuthorizationException (403) dengan redirect elegan
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            return redirect()->back()->withErrors(['access' => 'Akses ditolak: ' . $e->getMessage()]);
        });

    })
    ->create();