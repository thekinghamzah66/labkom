<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    // Relationships (Relasi Database)
    // -------------------------------------------------------------------------

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi untuk ASLAB (Many to Many)
     * Mengambil daftar praktikum yang dipegang oleh Aslab ini.
     */
    public function practicums(): BelongsToMany
    {
        return $this->belongsToMany(Practicum::class, 'aslab_practicum', 'user_id', 'practicum_id');
    }

    /**
     * Relasi untuk MAHASISWA (Has Many)
     * Mengambil daftar pendaftaran praktikum mahasiswa.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    /**
     * Relasi untuk DOSEN PEMBIMBING (Has Many)
     * Mengambil daftar mahasiswa bimbingan dosen ini.
     */
    public function mentoredEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'dosbim_id');
    }

    public function submissions(): HasMany 
    {
        return $this->hasMany(Submission::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
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
    // Helpers — Account Security
    // -------------------------------------------------------------------------

    public function isLocked(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until->greaterThanOrEqualTo(now()->subSecond());
    }

    public function recordFailedLogin(): void
    {
        $this->increment('login_attempts');
        if ($this->login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(30)]);
        }
    }

    public function resetLoginAttempts(): void
    {
        $this->update(['login_attempts' => 0, 'locked_until' => null]);
    }

    public function hasOAuth(): bool
    {
        return $this->google_id !== null;
    }
}