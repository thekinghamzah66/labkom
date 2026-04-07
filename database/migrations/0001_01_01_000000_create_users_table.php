<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users menggunakan role_id (FK) bukan enum/string langsung.
     * Alasan: referential integrity terjaga, query JOIN lebih efisien dengan index,
     * dan perubahan nama role tidak memerlukan update massal di tabel users.
     *
     * google_id diindex karena dipakai sebagai lookup key pada OAuth callback.
     * username diindex karena menjadi primary lookup pada form login.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->restrictOnDelete(); // Prevent orphan users jika role dihapus

            // Identitas
            $table->string('name', 100);
            $table->string('username', 50)->unique();    // NIM untuk mahasiswa, NIP untuk dosen/aslab
            $table->string('email', 150)->unique();
            $table->string('password')->nullable();       // Nullable untuk OAuth-only users
            $table->string('avatar', 500)->nullable();

            // OAuth
            $table->string('google_id', 100)->nullable()->unique();
            $table->string('google_token', 500)->nullable();
            $table->string('google_refresh_token', 500)->nullable();

            // Status & keamanan
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Audit trail: user tidak benar-benar dihapus

            // Composite index untuk query umum
            $table->index(['role_id', 'is_active']);     // Filter user aktif per role
            $table->index('username');
            $table->index('google_id');
            $table->index('email');
            $table->index('deleted_at');                 // Optimasi soft delete query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};