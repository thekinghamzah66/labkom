<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'module_id', 'file_path', 'score', 'feedback', 'status'];

    // Relasi ke User (Mahasiswa)
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Module
    public function module() {
        return $this->belongsTo(Module::class);
    }
}