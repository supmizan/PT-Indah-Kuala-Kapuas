<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    public function __construct(private PembayaranController $pembayaranController)
    {
    }

    private function getMitra()
    {
        return Auth::user()->mitra;
    }

    public function dashboard()
    {
        $mitra = $this->getMitra();
        if (!$mitra) {
            abort(404, 'Mitra profile not found.');
        }

        $stats = [
            'total_orders' => Pesanan::where('mitra_id', $mitra->id)->count(),
            'pending_orders' => Pesanan::where('mitra_id', $mitra->id)->where('status', 'pending')->count(),
            'process_orders' => Pesanan::where('mitra_id', $mitra->id)->where('status', 'diproses')->count(),
            'completed_orders' => Pesanan::where('mitra_id', $mitra->id)->where('status', 'selesai')->count(),
        ];

        $recent_orders = Pesanan::where('mitra_id', $mitra->id)->latest()->take(5)->get();

        return view('mitra.dashboard', compact('stats', 'recent_orders'));
    }

    public function createPesanan()
    {
        return view('mitra.pesanan.create');
    }

    public function storePesanan(Request $request)
    {
        $mitra = $this->getMitra();
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jumlah_bbm' => 'required|integer|min:1000|max:100000',
        ]);

        $pesanan = Pesanan::create([
            'mitra_id' => $mitra->id,
            'tanggal' => $request->tanggal,
            'jumlah_bbm' => $request->jumlah_bbm,
            'status' => 'pending',
        ]);

        $this->pembayaranController->buatPembayaran($pesanan, $mitra);

        return redirect()->route('mitra.pembayaran.show', $pesanan->id)
            ->with('success', 'Permintaan berhasil diajukan. Silakan selesaikan pembayaran terlebih dahulu.');
    }

    public function pesananIndex()
    {
        $mitra = $this->getMitra();
        $pesanans = Pesanan::where('mitra_id', $mitra->id)->with(['pengirimans.driver.user', 'pembayaran'])->latest()->paginate(10);
        return view('mitra.pesanan.index', compact('pesanans'));
    }

    public function trackPengiriman($id)
    {
        $mitra = $this->getMitra();
        // Ensure the pengiriman belongs to this mitra's pesanan
        $pengiriman = Pengiriman::with(['pesanan.mitra', 'driver.user', 'armada', 'trackings' => function($query){
            $query->latest();
        }])
        ->whereHas('pesanan', function ($query) use ($mitra) {
            $query->where('mitra_id', $mitra->id);
        })
        ->findOrFail($id);

        return view('mitra.pengiriman.track', compact('pengiriman'));
    }
}
