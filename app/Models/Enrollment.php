<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

protected $fillable = [
    'user_id', 
    'practicum_id', 
    'session_name', 
    'jam', 
    'ruangan', 
    'dosbim_id', 
    'nilai_awal', 
    'nilai_remidi', 
    'status_aslab',
    'soal_ujian_dosbim',    // WAJIB ADA INI
    'jawaban_ujian_dosbim',
    'nilai_ujian_dosbim',  
    'status_dosbim', 'nilai_remidi_dosbim' // WAJIB ADA INI
];

    // Relasi ke tabel User (Mahasiswa itu sendiri)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke tabel Practicum (PENTING: Ini yang bikin error tadi)
    public function practicum()
    {
        return $this->belongsTo(Practicum::class, 'practicum_id');
    }

    // Relasi ke tabel User (Dosen Pembimbing)
    public function dosbim()
    {
        return $this->belongsTo(User::class, 'dosbim_id');
    }
}