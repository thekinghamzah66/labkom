<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'google_id',
        'google_token',
        'google_refresh_token',
        'is_active',
        'email_verified_at',
        'login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_token',
        'google_refresh_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until'      => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'login_attempts'    => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $slug)
    {
        return $query->whereHas('role', fn($q) => $q->where('slug', $slug));
    }

    /**
     * Scope untuk AuthController::login()
     * Cari user berdasarkan username atau email (fleksibel)
     */
    public function scopeForLogin($query, string $identifier)
    {
        return $query->where(function ($q) use ($identifier) {
            $q->where('username', $identifier)
              ->orWhere('email', $identifier);
        });
    }

    // -------------------------------------------------------------------------
    // Helpers — Role Checks
    // -------------------------------------------------------------------------

    public function isKalab(): bool
    {
        return $this->role?->slug === 'kalab';
    }

    public function isMahasiswa(): bool
    {
        return $this->role?->slug === 'mahasiswa';
    }

    public function isAslab(): bool
    {
        return $this->role?->slug === 'aslab';
    }

    public function isDosenPembimbing(): bool
    {
        return $this->role?->slug === 'dosen-pembimbing';
    }

    // -------------------------------------------------------------------------
    // Helpers — Account Security (dipakai di AuthController)
    // -------------------------------------------------------------------------

    /**
     * Cek apakah akun sedang dikunci (locked_until masih di masa depan).
     * Dipakai di: AuthController::login()
     */
    public function isLocked(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until->greaterThanOrEqualTo(now()->subSecond());
    }

    /**
     * Tambah login_attempts dan kunci akun otomatis setelah 5 kali gagal.
     * Dipakai di: AuthController::login()
     */
    public function recordFailedLogin(): void
    {
        $this->increment('login_attempts');

        if ($this->login_attempts >= 5) {
            $this->update([
                'locked_until' => now()->addMinutes(30),
            ]);
        }
    }

    /**
     * Reset login_attempts dan buka kunci akun setelah login sukses.
     * Dipakai di: AuthController::login()
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until'   => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers — OAuth
    // -------------------------------------------------------------------------

    public function hasOAuth(): bool
    {
        return $this->google_id !== null;
    }
}
