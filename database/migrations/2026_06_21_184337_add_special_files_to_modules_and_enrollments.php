<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    // 1. Agar Aslab bisa kasih soal khusus remidi di tiap modul
    Schema::table('modules', function (Blueprint $table) {
        $table->string('file_remidi')->nullable(); 
    });

    // 2. Agar Dosbim bisa kasih soal ujian ke mahasiswa bimbingannya
    Schema::table('enrollments', function (Blueprint $table) {
        $table->string('soal_ujian_dosbim')->nullable();
        $table->string('jawaban_ujian_dosbim')->nullable();
        $table->integer('nilai_ujian_dosbim')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules_and_enrollments', function (Blueprint $table) {
            //
        });
    }
};
