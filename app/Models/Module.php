<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'practicum_id', // Tambahkan ini
        'title', 
        'description', 
        'type', 
        'file_path', 
        'deadline', 
        'is_approved'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    // Relasi ke Praktikum (Agar kita tahu materi ini milik praktikum apa)
    public function practicum()
    {
        return $this->belongsTo(Practicum::class);
    }

    // app/Models/Module.php

public function submissions()
{
    return $this->hasMany(Submission::class);
}

// Relasi khusus untuk mengecek submission milik user yang sedang login
public function userSubmission()
{
    return $this->hasOne(Submission::class)->where('user_id', auth()->id());
}
}