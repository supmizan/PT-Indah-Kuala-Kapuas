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
        Schema::table('armadas', function (Blueprint $table) {
            // Kode internal armada, terpisah dari plat nomor (contoh: ARM-001).
            // Dibuat nullable supaya tidak error kalau ada data lama yang belum punya kode.
            $table->string('kode_armada', 20)->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armadas', function (Blueprint $table) {
            $table->dropColumn('kode_armada');
        });
    }
};
