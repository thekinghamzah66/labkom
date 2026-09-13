<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Practicum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // -------------------------------------------------------------------------
    // Relationships (Relasi Database)
    // -------------------------------------------------------------------------

    /**
     * Relasi ke User (Aslab)
     * Menghubungkan praktikum dengan Asisten Lab melalui tabel pivot aslab_practicum.
     * Ini yang membuat praktikum otomatis muncul di dashboard Aslab yang dipilih Kalab.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'aslab_practicum', 'practicum_id', 'user_id');
    }

    /**
     * Relasi ke tabel Enrollment (Pendaftaran Mahasiswa)
     * Satu Praktikum memiliki banyak mahasiswa yang mendaftar.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'practicum_id');
    }

    /**
     * Relasi ke tabel Modules (Materi & Tugas)
     * Satu Praktikum memiliki banyak modul atau tugas yang diunggah oleh Aslab.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'practicum_id');
    }

    /**
     * Relasi ke tabel Attendances (Presensi)
     * Memantau daftar hadir mahasiswa di praktikum ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'practicum_id');
    }
}