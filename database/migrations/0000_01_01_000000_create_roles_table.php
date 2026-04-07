<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema roles dibuat sebagai tabel terpisah (bukan enum) untuk mendukung
     * skalabilitas: penambahan role baru tidak memerlukan ALTER TABLE pada tabel users.
     * Setiap role memiliki slug unik sebagai identifier yang aman untuk routing.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();        // 'Mahasiswa', 'Dosen Pembimbing', dll.
            $table->string('slug', 50)->unique();        // 'mahasiswa', 'dosen-pembimbing', dll.
            $table->string('description', 255)->nullable();
            $table->string('dashboard_route', 100);      // Nama route dashboard per role
            $table->string('color_class', 50)->nullable(); // Untuk UI Carousel
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk kolom yang sering di-query
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};