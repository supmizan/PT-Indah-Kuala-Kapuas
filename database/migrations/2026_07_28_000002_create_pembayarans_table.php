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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');

            // Order ID unik yang dikirim ke Midtrans (Midtrans mewajibkan order_id unik selamanya).
            $table->string('order_id')->unique();

            $table->decimal('jumlah_tagihan', 14, 2);
            $table->text('snap_token')->nullable();

            // menunggu   = pesanan dibuat, belum bayar
            // lunas      = pembayaran berhasil diverifikasi (dari webhook Midtrans)
            // gagal      = pembayaran ditolak/deny
            // kedaluwarsa= snap token/transaksi sudah expire
            $table->enum('status', ['menunggu', 'lunas', 'gagal', 'kedaluwarsa'])->default('menunggu');

            $table->string('metode_pembayaran')->nullable(); // contoh: bank_transfer, gopay, credit_card
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
