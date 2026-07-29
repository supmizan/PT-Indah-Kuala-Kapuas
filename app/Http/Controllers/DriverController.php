<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\Tracking;
use App\Models\Laporan;
use App\Models\Armada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    private function getDriver()
    {
        return Auth::user()->driver;
    }

    public function dashboard()
    {
        $driver = $this->getDriver();
        if (!$driver) {
            abort(404, 'Driver profile not found.');
        }

        // Active delivery
        $active_delivery = Pengiriman::with(['pesanan.mitra', 'armada'])
            ->where('driver_id', $driver->id)
            ->where('status', 'proses')
            ->first();

        // Delivery history
        $deliveries = Pengiriman::with(['pesanan.mitra', 'armada'])
            ->where('driver_id', $driver->id)
            ->latest()
            ->paginate(10);

        return view('driver.dashboard', compact('active_delivery', 'deliveries'));
    }

    public function updateLocation(Request $request, $id)
    {
        $driver = $this->getDriver();
        $pengiriman = Pengiriman::where('driver_id', $driver->id)
            ->where('status', 'proses')
            ->findOrFail($id);

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Tracking::create([
            'pengiriman_id' => $pengiriman->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'waktu' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Koordinat lokasi armada berhasil diperbarui secara real-time.'
        ]);
    }

    public function completePengiriman(Request $request, $id)
    {
        $driver = $this->getDriver();
        $pengiriman = Pengiriman::with('pesanan')->where('driver_id', $driver->id)
            ->where('status', 'proses')
            ->findOrFail($id);

        $request->validate([
            'keterangan' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $pengiriman) {
            // Update pengiriman status to selesai
            $pengiriman->update(['status' => 'selesai']);

            // Update pesanan status to selesai
            $pengiriman->pesanan->update(['status' => 'selesai']);

            // Release armada status to aktif
            $armada = Armada::findOrFail($pengiriman->armada_id);
            $armada->update(['status' => 'aktif']);

            // Create Laporan
            Laporan::create([
                'pengiriman_id' => $pengiriman->id,
                'keterangan' => $request->keterangan,
            ]);
        });

        return redirect()->route('driver.dashboard')->with('success', 'Pengiriman selesai dan laporan operasional telah dikirim.');
    }
}
