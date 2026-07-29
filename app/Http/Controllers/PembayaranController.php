<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Halaman pembayaran untuk 1 pesanan milik mitra yang login.
     */
    public function show($pesananId)
    {
        $mitra = Auth::user()->mitra;
        $pesanan = Pesanan::where('id', $pesananId)
            ->where('mitra_id', $mitra->id)
            ->firstOrFail();

        $pembayaran = $pesanan->pembayaran;

        // Kalau belum ada record pembayaran sama sekali (data lama sebelum fitur ini ada), buat sekarang.
        if (!$pembayaran) {
            $pembayaran = $this->buatPembayaran($pesanan, $mitra);
        }

        // Cek langsung ke Midtrans (bukan cuma menunggu webhook) — supaya kalau webhook belum
        // sempat/gagal masuk, status di sini tetap akurat begitu halaman ini dibuka lagi.
        $this->sinkronStatusDariMidtrans($pembayaran);

        if ($pembayaran->fresh()->status === 'lunas') {
            return redirect()->route('mitra.pesanan.index')
                ->with('success', 'Pesanan ini sudah dibayar.');
        }

        // Selalu generate ulang Snap token supaya tidak kedaluwarsa (Snap token Midtrans berlaku sekitar 24 jam).
        try {
            $pembayaran->snap_token = $this->buatSnapToken($pembayaran, $pesanan, $mitra);
            $pembayaran->save();
        } catch (\Exception $e) {
            // order_id sudah pernah dipakai untuk transaksi yang sudah final di sisi Midtrans
            // (misal sudah settlement) tapi status lokal kita belum sempat ikut ter-update.
            // Buat order_id baru supaya mitra tetap bisa lanjut/ulangi pembayaran.
            Log::warning('Gagal buat Snap token, membuat order_id baru', ['pesanan_id' => $pesanan->id, 'error' => $e->getMessage()]);

            $pembayaran->order_id = 'PTIKK-' . $pesanan->id . '-' . time();
            $pembayaran->snap_token = $this->buatSnapToken($pembayaran, $pesanan, $mitra);
            $pembayaran->save();
        }

        return view('mitra.pembayaran.bayar', [
            'pesanan' => $pesanan,
            'pembayaran' => $pembayaran,
            'clientKey' => config('midtrans.client_key'),
            'isProduction' => config('midtrans.is_production'),
        ]);
    }

    /**
     * Tanya langsung ke Midtrans status transaksi terakhir untuk order_id ini,
     * lalu simpan hasilnya ke tabel pembayarans. Dipakai sebagai cadangan kalau webhook
     * belum/tidak sampai ke server ini (misal saat dev lokal tanpa ngrok).
     */
    private function sinkronStatusDariMidtrans(Pembayaran $pembayaran): void
    {
        try {
            $status = Transaction::status($pembayaran->order_id);
        } catch (\Exception $e) {
            // Transaksi belum pernah dibuat di Midtrans sama sekali (order baru) — wajar, abaikan saja.
            return;
        }

        $this->terapkanStatus($pembayaran, $status->transaction_status, $status->fraud_status ?? null, $status->payment_type ?? null);
    }

    private function terapkanStatus(Pembayaran $pembayaran, string $transactionStatus, ?string $fraudStatus, ?string $paymentType): void
    {
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'challenge') {
                $pembayaran->status = 'menunggu';
            } else {
                $pembayaran->status = 'lunas';
                $pembayaran->paid_at = now();
            }
        } elseif ($transactionStatus === 'pending') {
            $pembayaran->status = 'menunggu';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $pembayaran->status = 'gagal';
        } elseif ($transactionStatus === 'expire') {
            $pembayaran->status = 'kedaluwarsa';
        }

        $pembayaran->metode_pembayaran = $paymentType;
        $pembayaran->save();
    }

    /**
     * Dipanggil dari MitraController@storePesanan setelah pesanan baru dibuat.
     */
    public function buatPembayaran(Pesanan $pesanan, $mitra): Pembayaran
    {
        $totalTagihan = $pesanan->jumlah_bbm * $mitra->harga_per_liter;

        return Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'order_id' => 'PTIKK-' . $pesanan->id . '-' . time(),
            'jumlah_tagihan' => $totalTagihan,
            'status' => 'menunggu',
        ]);
    }

    private function buatSnapToken(Pembayaran $pembayaran, Pesanan $pesanan, $mitra): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $pembayaran->order_id,
                'gross_amount' => (int) $pembayaran->jumlah_tagihan,
            ],
            'customer_details' => [
                'first_name' => $mitra->nama_perusahaan,
                'phone' => $mitra->no_hp,
            ],
            'item_details' => [[
                'id' => 'bbm-' . $pesanan->id,
                'price' => (int) $mitra->harga_per_liter,
                'quantity' => (int) $pesanan->jumlah_bbm,
                'name' => 'BBM (liter) - Pesanan #' . $pesanan->id,
            ]],
            // Batasi opsi yang muncul di popup Snap jadi 3 kelompok saja:
            // - QRIS
            // - Virtual Account (gabungan beberapa bank, Snap otomatis mengelompokkannya)
            // - Card Payment (kartu kredit/debit)
            'enabled_payments' => [
                'credit_card',
                'gopay',
                'bca_va',
                'bni_va',
                'bri_va',
                'permata_va',
                'cimb_va',
                'echannel',
                'other_va',
            ],
            // Arahkan balik ke halaman bayar ini sendiri (bukan langsung ke daftar pesanan) —
            // supaya begitu kembali, sinkronStatusDariMidtrans() di show() langsung jalan
            // dan otomatis mengecek + update status terbaru ke Midtrans.
            'callbacks' => [
                'finish' => route('mitra.pembayaran.show', $pesanan->id),
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Webhook notifikasi dari server Midtrans (bukan dari browser user).
     * Route ini dikecualikan dari CSRF di bootstrap/app.php.
     */
    public function notification()
    {
        $notif = new Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status ?? null;
        $paymentType = $notif->payment_type ?? null;

        Log::info('Midtrans notification diterima', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (!$pembayaran) {
            Log::warning('Midtrans notification: order_id tidak ditemukan', ['order_id' => $orderId]);
            return response()->json(['message' => 'order_id tidak ditemukan'], 404);
        }

        $this->terapkanStatus($pembayaran, $transactionStatus, $fraudStatus, $paymentType);

        return response()->json(['message' => 'OK']);
    }
}
