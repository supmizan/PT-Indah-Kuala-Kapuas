<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Halaman pembayaran (tampilkan QRIS statis + form upload bukti transfer)
     * untuk 1 pesanan milik mitra yang login.
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

        return view('mitra.pembayaran.bayar', [
            'pesanan' => $pesanan,
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Dipanggil dari MitraController@storePesanan setelah pesanan baru dibuat.
     */
    public function buatPembayaran(Pesanan $pesanan, $mitra): Pembayaran
    {
        $totalTagihan = $pesanan->jumlah_bbm * $mitra->harga_per_liter;

        return Pembayaran::create([
            'pesanan_id' => $pesanan->id,
            'order_id' => 'PTIKK-' . $pesanan->id . '-' . time(), // sekadar nomor referensi internal
            'jumlah_tagihan' => $totalTagihan,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Mitra upload bukti transfer.
     */
    public function upload(Request $request, $pesananId)
    {
        $mitra = Auth::user()->mitra;
        $pesanan = Pesanan::where('id', $pesananId)
            ->where('mitra_id', $mitra->id)
            ->firstOrFail();

        $pembayaran = $pesanan->pembayaran;

        if (!$pembayaran || in_array($pembayaran->status, ['lunas'])) {
            return redirect()->route('mitra.pesanan.index')
                ->with('error', 'Pesanan ini tidak memerlukan upload bukti transfer lagi.');
        }

        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Hapus file bukti lama kalau ada (misal mitra upload ulang setelah ditolak admin).
        if ($pembayaran->bukti_transfer) {
            Storage::disk('local')->delete($pembayaran->bukti_transfer);
        }

        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'local');

        $pembayaran->update([
            'bukti_transfer' => $path,
            'status' => 'menunggu_verifikasi',
            'catatan_admin' => null,
        ]);

        return redirect()->route('mitra.pesanan.index')
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi dari admin.');
    }

    /**
     * Admin melihat gambar bukti transfer (file disimpan privat, jadi diserve lewat route
     * yang dilindungi middleware admin, bukan URL publik langsung).
     */
    public function lihatBukti(Pembayaran $pembayaran)
    {
        if (!$pembayaran->bukti_transfer || !Storage::disk('local')->exists($pembayaran->bukti_transfer)) {
            abort(404, 'Bukti transfer tidak ditemukan.');
        }

        return Storage::disk('local')->response($pembayaran->bukti_transfer);
    }

    /**
     * Admin konfirmasi pembayaran ini lunas.
     */
    public function verifikasi(Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status' => 'lunas',
            'diverifikasi_oleh' => Auth::id(),
            'diverifikasi_at' => now(),
            'catatan_admin' => null,
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Pembayaran pesanan #' . $pembayaran->pesanan_id . ' dikonfirmasi lunas.');
    }

    /**
     * Admin menolak bukti transfer (misal buram/tidak sesuai nominal), mitra diminta upload ulang.
     */
    public function tolak(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:255',
        ]);

        $pembayaran->update([
            'status' => 'ditolak',
            'diverifikasi_oleh' => Auth::id(),
            'diverifikasi_at' => now(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Bukti transfer pesanan #' . $pembayaran->pesanan_id . ' ditolak, mitra diminta upload ulang.');
    }
}