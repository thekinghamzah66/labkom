<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleRedirectRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use App\Services\Auth\AuthenticationService;
use App\Services\Auth\GoogleOAuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class AuthController extends Controller
{
    public function __construct(
        private AuthenticationService $authService,
        private GoogleOAuthService $oauthService,
    ) {}

    public function showRoleSelection(): View|RedirectResponse
    {
        $roles = Role::active()->orderBy('id')->get();

        // ✅ Siapkan JSON di controller, hindari arrow function di Blade
        $rolesJson = $roles->map(function ($r) {
            return [
                'id'   => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
            ];
        })->values();

        return view('welcome', compact('roles', 'rolesJson'));
    }

    public function showLogin(string $role_slug): View|RedirectResponse
    {
        $selectedRole = Role::bySlug($role_slug)->active()->firstOrFail();
        return view('auth.login', compact('selectedRole'));
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->authService->recordFailedLogin(
                null,
                $request,
                "IP terkunci. Coba lagi dalam {$seconds} detik.",
                'failed_locked'
            );

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik.",
            ]);
        }

        try {
            $user = $this->authService->authenticate(
                $request->username,
                $request->password,
                $request->role_selected
            );

            RateLimiter::clear($throttleKey);
            $this->authService->recordSuccessfulLogin($user, $request);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectToDashboard($user);

        } catch (AuthenticationException $e) {
            RateLimiter::hit($throttleKey, 60);

            $this->authService->recordFailedLogin(
                null,
                $request,
                $e->getMessage(),
                'failed_' . $this->getFailureType($e->getMessage())
            );

            throw ValidationException::withMessages([
                'username' => $e->getMessage(),
            ]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->recordFailedLogin(
            Auth::user(),
            $request,
            '',
            'logout'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')
                        ->with('status', 'Berhasil keluar dari sistem.');
    }

    public function redirectToGoogle(GoogleRedirectRequest $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        session(['oauth_role_selected' => $request->role_selected]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $roleSelected = session()->pull('oauth_role_selected');

        if (!$roleSelected) {
            return redirect()->route('welcome')
                ->withErrors(['role_selected' => 'Sesi role hilang, silakan pilih ulang.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('welcome')
                ->withErrors(['username' => 'Gagal terhubung ke Google. Silakan coba lagi.']);
        }

        assert($googleUser instanceof SocialiteUser);

        try {
            $user = $this->oauthService->authenticate($googleUser, $roleSelected);
            $this->oauthService->updateUserWithGoogleData($user, $googleUser);
            $this->oauthService->recordSuccessfulLogin($user, $request);

            Auth::login($user, true);
            $request->session()->regenerate();

            return $this->redirectToDashboard($user);

        } catch (AuthenticationException $e) {
            return redirect()->route('welcome')
                ->withErrors(['username' => $e->getMessage()]);
        }
    }

    private function redirectToDashboard(\App\Models\User $user): RedirectResponse
    {
        return redirect()->route($user->role->dashboard_route);
    }

    private function getFailureType(string $message): string
    {
        return match (true) {
            str_contains($message, 'password') => 'credentials',
            str_contains($message, 'role')     => 'role_mismatch',
            str_contains($message, 'dinonaktifkan') => 'inactive',
            str_contains($message, 'dikunci')  => 'locked',
            default                            => 'unknown',
        };
    }
}
