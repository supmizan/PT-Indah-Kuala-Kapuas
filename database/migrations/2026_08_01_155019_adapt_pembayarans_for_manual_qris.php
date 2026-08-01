<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah status: menunggu (belum bayar) -> menunggu_verifikasi (sudah upload bukti,
        // nunggu dicek admin) -> lunas / ditolak.
        //
        // "MODIFY ... ENUM" adalah sintaks khusus MySQL (dipakai di production/Railway).
        // SQLite (dipakai test suite, lihat phpunit.xml) tidak punya ALTER MODIFY/ENUM sama sekali,
        // jadi untuk SQLite kita drop lalu buat ulang kolomnya sebagai string biasa dengan
        // default yang sama — secara fungsional setara (mengizinkan nilai status yang baru),
        // hanya saja tanpa validasi ENUM di level database (validasi tetap dilakukan aplikasi).
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->after('jumlah_tagihan');
            });
        } else {
            DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('menunggu', 'menunggu_verifikasi', 'lunas', 'ditolak') NOT NULL DEFAULT 'menunggu'");
        }

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('metode_pembayaran');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('bukti_transfer')->constrained('users')->onDelete('set null');
            $table->timestamp('diverifikasi_at')->nullable()->after('diverifikasi_oleh');
            $table->text('catatan_admin')->nullable()->after('diverifikasi_at');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn(['bukti_transfer', 'diverifikasi_oleh', 'diverifikasi_at', 'catatan_admin']);
        });

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->after('jumlah_tagihan');
            });
        } else {
            DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('menunggu', 'lunas', 'gagal', 'kedaluwarsa') NOT NULL DEFAULT 'menunggu'");
        }
    }
};