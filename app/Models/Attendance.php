<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // TAMBAHKAN BARIS INI
    protected $fillable = [
    'user_id',
    'practicum_id',
    'date',
    'status',
    'session_token', // Jika kamu masih menggunakan token unik
    'keterangan'     // Untuk catatan tambahan jika diperlukan
];

    // Relasi ke User agar nama mahasiswa muncul di sisi Aslab
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}