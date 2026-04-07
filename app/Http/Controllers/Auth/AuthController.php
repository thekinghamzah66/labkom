<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Perbaikan dari Facadesx
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // -------------------------------------------------------------------------
    // 1. Halaman Pilihan Role (Carousel) - Route: /
    // -------------------------------------------------------------------------
    public function showRoleSelection()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Ambil role aktif untuk ditampilkan di carousel welcome
        $roles = Role::active()->orderBy('id')->get();

        return view('welcome', compact('roles'));
    }

    // -------------------------------------------------------------------------
    // 2. Halaman Form Login (Setelah Pilih Role) - Route: /login/{role}
    // -------------------------------------------------------------------------
    public function showLogin($role_slug)
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Cari role berdasarkan slug (mahasiswa, aslab, dll)
        $selectedRole = Role::where('slug', $role_slug)->active()->firstOrFail();

        return view('auth.login', compact('selectedRole'));
    }

    // -------------------------------------------------------------------------
    // 3. Login via Form (Username + Password)
    // -------------------------------------------------------------------------
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username'      => ['required', 'string', 'max:100'],
            'password'      => ['required', 'string'],
            'role_selected' => ['required', 'string', 'exists:roles,slug'],
        ], [
            'role_selected.required' => 'Terjadi kesalahan sistem, role tidak terdeteksi.',
        ]);

        $throttleKey = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->writeLog(null, $request, 'failed_locked', "IP terkunci. Coba lagi dalam {$seconds} detik.");

            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik.",
            ]);
        }

        /** @var User|null $user */
        $user = User::forLogin($request->username)->first();

        // Validasi Kredensial
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->writeLog($user, $request, 'failed_credentials', 'Username/password salah.');

            if ($user) { $user->recordFailedLogin(); }

            throw ValidationException::withMessages([
                'username' => 'Kombinasi username dan password tidak ditemukan.',
            ]);
        }

        // Proteksi Role Mismatch (Pilih Mahasiswa tapi login pakai akun Kalab)
        if ($user->role->slug !== $request->role_selected) {
            RateLimiter::hit($throttleKey, 60);
            $this->writeLog($user, $request, 'failed_role_mismatch', "User {$user->role->name} mencoba login sebagai {$request->role_selected}.");

            throw ValidationException::withMessages([
                'username' => "Akun Anda terdaftar sebagai {$user->role->name}. Silakan kembali ke halaman awal dan pilih role yang benar.",
            ]);
        }

        // Cek Status Aktif & Lockout
        if (!$user->is_active) {
            $this->writeLog($user, $request, 'failed_inactive', 'Akun nonaktif.');
            throw ValidationException::withMessages(['username' => 'Akun Anda dinonaktifkan oleh admin.']);
        }

        if ($user->isLocked()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            throw ValidationException::withMessages(['username' => "Akun sedang dikunci. Tersisa {$minutes} menit lagi."]);
        }

        // Login Sukses
        RateLimiter::clear($throttleKey);
        $user->resetLoginAttempts();
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $this->writeLog($user, $request, 'success');

        return $this->redirectToDashboard($user);
    }

    // -------------------------------------------------------------------------
    // 4. Logout
    // -------------------------------------------------------------------------
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('status', 'Berhasil keluar dari sistem.');
    }

    // -------------------------------------------------------------------------
    // 5. Google OAuth Logic
    // -------------------------------------------------------------------------
    public function redirectToGoogle(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $request->validate(['role_selected' => ['required', 'exists:roles,slug']]);
        
        // Simpan role ke session agar saat balik dari Google kita tahu user mau masuk sebagai apa
        session(['oauth_role_selected' => $request->role_selected]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $roleSelected = session()->pull('oauth_role_selected');

        if (!$roleSelected) {
            return redirect()->route('welcome')->withErrors(['role_selected' => 'Sesi role hilang, silakan pilih ulang.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('welcome')->withErrors(['username' => 'Gagal terhubung ke Google.']);
        }

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            return redirect()->route('welcome')->withErrors(['username' => 'Email Google Anda belum terdaftar.']);
        }

        // Cek apakah role akun Google sesuai dengan yang dipilih di awal
        if ($user->role->slug !== $roleSelected) {
            return redirect()->route('login', $roleSelected)->withErrors([
                'username' => "Email ini terdaftar sebagai {$user->role->name}, bukan {$roleSelected}."
            ]);
        }

        // Update data Google jika diperlukan
        $user->update([
            'google_id'    => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'avatar'       => $user->avatar ?? $googleUser->getAvatar(),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->writeLog($user, $request, 'success', 'Google OAuth Login');

        return $this->redirectToDashboard($user);
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------
    private function redirectToDashboard(User $user): RedirectResponse
    {
        return redirect()->route($user->role->dashboard_route);
    }

    private function writeLog(?User $user, Request $request, string $status, ?string $reason = null): void
    {
        LoginLog::create([
            'user_id'            => $user?->id,
            'username_attempted' => $request->input('username', $user?->username ?? 'oauth'),
            'role_attempted'     => $request->input('role_selected', session('oauth_role_selected', 'unknown')),
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'status'             => $status,
            'failure_reason'     => $reason,
            'attempted_at'       => now(),
        ]);
    }
}