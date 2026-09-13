<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('enrollments', function (Blueprint $table) {
        // Tambahkan kolom untuk soal dan jawaban bimbingan
        if (!Schema::hasColumn('enrollments', 'soal_ujian_dosbim')) {
            $table->string('soal_ujian_dosbim')->nullable()->after('dosbim_id');
        }
        if (!Schema::hasColumn('enrollments', 'jawaban_ujian_dosbim')) {
            $table->string('jawaban_ujian_dosbim')->nullable()->after('soal_ujian_dosbim');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
        });
    }
};
