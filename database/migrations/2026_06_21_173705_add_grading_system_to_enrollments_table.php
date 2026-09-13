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
        $table->integer('nilai_awal')->nullable();
        $table->integer('nilai_remidi')->nullable();
        // Status: pending, lulus, remidi, gagal
        $table->string('status_aslab')->default('pending'); 
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
