<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah status: menunggu (belum bayar) -> menunggu_verifikasi (sudah upload bukti,
        // nunggu dicek admin) -> lunas / ditolak.
        DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('menunggu', 'menunggu_verifikasi', 'lunas', 'ditolak') NOT NULL DEFAULT 'menunggu'");

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('metode_pembayaran');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('bukti_transfer')->constrained('users')->onDelete('set null');
            $table->timestamp('diverifikasi_at')->nullable()->after('diverifikasi_oleh');
            $table->text('catatan_admin')->nullable()->after('diverifikasi_at'); // alasan kalau ditolak
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn(['bukti_transfer', 'diverifikasi_oleh', 'diverifikasi_at', 'catatan_admin']);
        });

        DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('menunggu', 'lunas', 'gagal', 'kedaluwarsa') NOT NULL DEFAULT 'menunggu'");
    }
};
