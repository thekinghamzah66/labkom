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
    Schema::table('modules', function (Blueprint $table) {
        // Tambahkan kolom is_approved (default 0 / false)
        $table->boolean('is_approved')->default(false)->after('type');
    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('modules', function (Blueprint $table) {
        $table->dropColumn('is_approved');
    });
}
};
