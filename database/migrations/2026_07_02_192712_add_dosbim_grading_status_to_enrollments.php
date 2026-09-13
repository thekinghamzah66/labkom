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
    Schema::table('enrollments', function (Blueprint $table) {
        $table->string('status_dosbim')->default('pending')->after('nilai_ujian_dosbim');
        $table->integer('nilai_remidi_dosbim')->nullable()->after('status_dosbim');
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
