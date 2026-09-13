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
        // 1. Tambah practicum_id ke tabel Modules (Materi)
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'practicum_id')) {
                $table->foreignId('practicum_id')->nullable()->after('id')->constrained('practicums')->onDelete('cascade');
            }
        });

        // 2. Tambah practicum_id ke tabel Attendances (Absensi)
        // Sesuaikan nama tabel absensi kamu (biasanya 'attendances')
        if (Schema::hasTable('attendances')) {
            Schema::table('attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('attendances', 'practicum_id')) {
                    $table->foreignId('practicum_id')->nullable()->after('id')->constrained('practicums')->onDelete('cascade');
                }
            });
        }

        // 3. Tambah practicum_id ke tabel Grades/Scores (Penilaian)
        // Sesuaikan nama tabel penilaian kamu (misal 'grades' atau 'scores')
        if (Schema::hasTable('grades')) {
            Schema::table('grades', function (Blueprint $table) {
                if (!Schema::hasColumn('grades', 'practicum_id')) {
                    $table->foreignId('practicum_id')->nullable()->after('id')->constrained('practicums')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['practicum_id']);
            $table->dropColumn('practicum_id');
        });
        // Lakukan hal yang sama untuk tabel lain jika ingin rollback
    }
};
