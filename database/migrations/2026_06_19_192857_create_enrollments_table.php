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
    Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users'); // Mahasiswa
        $table->foreignId('practicum_id')->constrained('practicums');
        // Kolom sesi (diketik manual nanti)
        $table->string('session_name')->nullable(); // Sesi 1
        $table->string('jam')->nullable();          // 18:00
        $table->string('ruangan')->nullable();      // A101
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
