<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'dashboard_route',
        'color_class',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // -------------------------------------------------------------------------
    // Query Scopes — mencegah N+1 dan menyederhanakan query di controller
    // -------------------------------------------------------------------------

    /**
     * Scope: hanya role yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: cari role berdasarkan slug.
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isForMahasiswa(): bool
    {
        return $this->slug === 'mahasiswa';
    }

    public function isForDosen(): bool
    {
        return $this->slug === 'dosen-pembimbing';
    }
}