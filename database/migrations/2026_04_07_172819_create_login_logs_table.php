<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit log untuk merekam setiap percobaan login.
     * Penting untuk keamanan, forensik, dan deteksi brute-force.
     * Dipisah dari tabel users agar tidak membebani tabel utama.
     */
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username_attempted', 100); // Log username yang dicoba walau user tidak ditemukan
            $table->string('role_attempted', 50);       // Role yang dipilih di carousel
            $table->string('ip_address', 45);           // IPv4/IPv6
            $table->string('user_agent', 500)->nullable();
            $table->enum('status', ['success', 'failed_credentials', 'failed_role_mismatch', 'failed_inactive', 'failed_locked']);
            $table->string('failure_reason', 255)->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            // Index untuk query forensik dan monitoring
            $table->index(['ip_address', 'attempted_at']); // Deteksi brute-force per IP
            $table->index(['user_id', 'attempted_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};