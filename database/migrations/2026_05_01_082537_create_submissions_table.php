<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID Mahasiswa
        $table->foreignId('module_id')->constrained()->onDelete('cascade'); // ID Modul
        $table->string('file_path');
        $table->integer('score')->nullable();
        $table->text('feedback')->nullable();
        $table->string('status')->default('Pending'); // Pending atau Graded
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
