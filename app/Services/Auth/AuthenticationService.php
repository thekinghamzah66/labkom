<?php

namespace App\Services\Auth;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    /**
     * Authenticate a user with username/email and password.
     *
     * @param string $identifier Username or email
     * @param string $password   Plain text password
     * @param string $requestedRole Role slug the user is trying to authenticate as
     *
     * @throws AuthenticationException on any validation failure
     */
    public function authenticate(string $identifier, string $password, string $requestedRole): User
    {
        // Find user by username or email, with role eager loaded
        $user = User::forLogin($identifier)
                    ->with('role')
                    ->first();

        // 1️⃣ Validate credentials (intentionally same message for both failures)
        if (!$user || !Hash::check($password, $user->password)) {
            throw new AuthenticationException(
                'Kombinasi username dan password tidak ditemukan.'
            );
        }

        // 2️⃣ Check if account is active
        if (!$user->is_active) {
            throw new AuthenticationException(
                'Akun Anda dinonaktifkan oleh admin.'
            );
        }

        // 3️⃣ Check if account is locked
        if ($user->isLocked()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            throw new AuthenticationException(
                "Akun sedang dikunci. Tersisa {$minutes} menit lagi."
            );
        }

        // 4️⃣ Validate role matches
        if ($user->role->slug !== $requestedRole) {
            throw new AuthenticationException(
                "Akun Anda terdaftar sebagai {$user->role->name}. "
                ."Silakan kembali ke halaman awal dan pilih role yang benar."
            );
        }

        return $user;
    }

    /**
     * Record a successful login attempt.
     */
    public function recordSuccessfulLogin(User $user, Request $request, string $reason = 'Form Login'): void
    {
        $user->resetLoginAttempts();

        $this->logLoginAttempt(
            user: $user,
            request: $request,
            status: 'success',
            reason: $reason
        );
    }

    /**
     * Record a failed login attempt with the given reason.
     */
    public function recordFailedLogin(User|null $user, Request $request, string $reason, string $status): void
    {
        if ($user && $status === 'failed_credentials') {
            $user->recordFailedLogin();
        }

        $this->logLoginAttempt(
            user: $user,
            request: $request,
            status: $status,
            reason: $reason
        );
    }

    /**
     * Log a login attempt to the database.
     *
     * @param User|null $user The user, or null if user not found
     * @param Request $request The HTTP request
     * @param string $status The login status (success, failed_credentials, etc)
     * @param string|null $reason Optional failure reason
     */
    private function logLoginAttempt(
        User|null $user,
        Request $request,
        string $status,
        string|null $reason = null
    ): void {
        LoginLog::create([
            'user_id'            => $user?->id,
            'username_attempted' => $request->input('username', $user?->username ?? 'unknown'),
            'role_attempted'     => $request->input('role_selected', 'unknown'),
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'status'             => $status,
            'failure_reason'     => $reason,
            'attempted_at'       => now(),
        ]);
    }
}
