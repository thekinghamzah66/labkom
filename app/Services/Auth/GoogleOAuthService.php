<?php

namespace App\Services\Auth;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\User as SocialiteUser;

class GoogleOAuthService
{
    /**
     * Authenticate a user via Google OAuth.
     *
     * @param SocialiteUser $googleUser The user object from Socialite
     * @param string $requestedRole The role slug the user selected
     *
     * @throws AuthenticationException on any validation failure
     */
    public function authenticate(SocialiteUser $googleUser, string $requestedRole): User
    {
        // Find user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->with('role')
                    ->first();

        if (!$user) {
            throw new AuthenticationException(
                'Email Google Anda belum terdaftar di sistem.'
            );
        }

        // Check if account is active
        if (!$user->is_active) {
            throw new AuthenticationException(
                'Akun Anda dinonaktifkan oleh admin.'
            );
        }

        // Check if role matches
        if ($user->role->slug !== $requestedRole) {
            throw new AuthenticationException(
                "Email ini terdaftar sebagai {$user->role->name}, bukan {$requestedRole}."
            );
        }

        return $user;
    }

    /**
     * Update user with Google OAuth tokens and avatar.
     */
    public function updateUserWithGoogleData(User $user, SocialiteUser $googleUser): void
    {
        $user->update([
            'google_id'            => $googleUser->getId(),
            'google_token'         => $googleUser->token,
            // Preserve existing refresh token if new one is null
            'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
            // Only update avatar if user doesn't have one
            'avatar'               => $user->avatar ?? $googleUser->getAvatar(),
        ]);
    }

    /**
     * Log a successful OAuth login.
     */
    public function recordSuccessfulLogin(User $user, Request $request): void
    {
        LoginLog::create([
            'user_id'            => $user->id,
            'username_attempted' => $user->username,
            'role_attempted'     => $user->role->slug,
            'ip_address'         => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'status'             => 'success',
            'failure_reason'     => 'Google OAuth Login',
            'attempted_at'       => now(),
        ]);
    }
}
