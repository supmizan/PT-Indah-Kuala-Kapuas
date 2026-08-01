<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\Mitra;
use App\Models\Armada;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Models\Pengiriman;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'drivers' => Driver::count(),
            'mitras' => Mitra::count(),
            'armadas' => Armada::count(),
            'active_deliveries' => Pengiriman::where('status', 'proses')->count(),
            'pending_orders' => Pesanan::where('status', 'pending')->count(),
        ];

        // Fetch recent activities
        $recent_deliveries = Pengiriman::with(['pesanan.mitra', 'driver.user', 'armada'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_deliveries'));
    }

    // --- DRIVER CRUD ---
    public function driverIndex()
    {
        $drivers = Driver::with('user')->paginate(10);
        return view('admin.driver.index', compact('drivers'));
    }

    public function driverCreate()
    {
        return view('admin.driver.create');
    }

    public function driverStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'driver',
            ]);

            Driver::create([
                'user_id' => $user->id,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'status' => 'aktif',
            ]);
        });

        return redirect()->route('admin.driver.index')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function driverEdit($id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        return view('admin.driver.edit', compact('driver'));
    }

    public function driverUpdate(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $driver->user_id,
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request, $driver) {
            $driver->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $driver->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $driver->update([
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.driver.index')->with('success', 'Data driver berhasil diperbarui.');
    }

    public function driverDestroy($id)
    {
        $driver = Driver::findOrFail($id);
        DB::transaction(function () use ($driver) {
            $user = $driver->user;
            $driver->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.driver.index')->with('success', 'Driver berhasil dihapus.');
    }

    // --- MITRA CRUD ---
    public function mitraIndex()
    {
        $mitras = Mitra::with('user')->paginate(10);
        return view('admin.mitra.index', compact('mitras'));
    }

    public function mitraCreate()
    {
        return view('admin.mitra.create');
    }

    public function mitraStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nama_perusahaan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'harga_per_liter' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mitra',
            ]);

            Mitra::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'no_hp' => $request->no_hp,
                'harga_per_liter' => $request->harga_per_liter,
            ]);
        });

        return redirect()->route('admin.mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function mitraEdit($id)
    {
        $mitra = Mitra::with('user')->findOrFail($id);
        return view('admin.mitra.edit', compact('mitra'));
    }

    public function mitraUpdate(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mitra->user_id,
            'nama_perusahaan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'harga_per_liter' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $mitra) {
            $mitra->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $mitra->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $mitra->update([
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'no_hp' => $request->no_hp,
                'harga_per_liter' => $request->harga_per_liter,
            ]);

            // Kalau harga per liter berubah, sesuaikan ulang total tagihan pesanan yang
            // BELUM ada bukti transfer diupload (status: menunggu / ditolak) supaya pakai harga terbaru.
            // Pesanan yang sudah "menunggu_verifikasi" (mitra sudah upload bukti dengan nominal lama)
            // atau sudah "lunas" TIDAK disentuh, supaya tidak mengubah nominal yang sudah dibayar/sedang direview.
            Pembayaran::whereIn('status', ['menunggu', 'ditolak'])
                ->whereHas('pesanan', function ($query) use ($mitra) {
                    $query->where('mitra_id', $mitra->id);
                })
                ->with('pesanan')
                ->get()
                ->each(function ($pembayaran) use ($request) {
                    $pembayaran->update([
                        'jumlah_tagihan' => $pembayaran->pesanan->jumlah_bbm * $request->harga_per_liter,
                    ]);
                });
        });

        return redirect()->route('admin.mitra.index')->with('success', 'Data mitra berhasil diperbarui.');
    }

    public function mitraDestroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        DB::transaction(function () use ($mitra) {
            $user = $mitra->user;
            $mitra->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }

    // --- ARMADA CRUD ---
    public function armadaIndex()
    {
        $armadas = Armada::paginate(10);
        $total_armada = Armada::count();
        $aktif_armada = Armada::where('status', 'aktif')->count();
        $maintenance_armada = Armada::where('status', 'maintenance')->count();
        return view('admin.armada.index', compact('armadas', 'total_armada', 'aktif_armada', 'maintenance_armada'));
    }

    public function armadaCreate()
    {
        return view('admin.armada.create');
    }

    public function armadaStore(Request $request)
    {
        $request->validate([
            'kode_armada' => 'required|string|max:20|unique:armadas,kode_armada',
            'no_polisi' => 'required|string|max:15|unique:armadas,no_polisi',
            'jenis' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:aktif,digunakan,maintenance',
        ]);

        Armada::create($request->all());

        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil ditambahkan.');
    }

    public function armadaEdit($id)
    {
        $armada = Armada::findOrFail($id);
        return view('admin.armada.edit', compact('armada'));
    }

    public function armadaUpdate(Request $request, $id)
    {
        $armada = Armada::findOrFail($id);
        $request->validate([
            'kode_armada' => 'required|string|max:20|unique:armadas,kode_armada,' . $id,
            'no_polisi' => 'required|string|max:15|unique:armadas,no_polisi,' . $id,
            'jenis' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:aktif,digunakan,maintenance',
        ]);

        $armada->update($request->all());

        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil diperbarui.');
    }

    public function armadaDestroy($id)
    {
        $armada = Armada::findOrFail($id);
        $armada->delete();

        return redirect()->route('admin.armada.index')->with('success', 'Armada berhasil dihapus.');
    }

    // --- PESANAN & DISPATCH ---
    public function pesananIndex()
    {
        $pesanans = Pesanan::with(['mitra', 'pembayaran'])->latest()->paginate(10);

        return view('admin.pesanan.index', compact('pesanans'));
    }

    public function pesananDispatchForm($id)
    {
        $pesanan = Pesanan::with(['mitra', 'pembayaran'])->findOrFail($id);

        if (!$pesanan->sudahLunas()) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan #' . $pesanan->id . ' belum dibayar oleh mitra. Tidak bisa dijadwalkan.');
        }

        $drivers = Driver::with('user')->where('status', 'aktif')->get();
        $armadas = Armada::where('status', 'aktif')->get();

        return view('admin.pesanan.dispatch', compact('pesanan', 'drivers', 'armadas'));
    }

    public function pesananDispatch(Request $request, $id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);

        if (!$pesanan->sudahLunas()) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan #' . $pesanan->id . ' belum dibayar oleh mitra. Tidak bisa dijadwalkan.');
        }

        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'armada_id' => 'required|exists:armadas,id',
            'tanggal_kirim' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $pesanan) {
            // Update pesanan status
            $pesanan->update(['status' => 'diproses']);

            // Create Pengiriman
            Pengiriman::create([
                'pesanan_id' => $pesanan->id,
                'driver_id' => $request->driver_id,
                'armada_id' => $request->armada_id,
                'tanggal_kirim' => $request->tanggal_kirim,
                'status' => 'proses',
            ]);

            // Update Armada status
            $armada = Armada::findOrFail($request->armada_id);
            $armada->update(['status' => 'digunakan']);
        });

        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dijadwalkan dan ditugaskan.');
    }

    // --- PENGIRIMAN & LAPORAN ---
    public function pengirimanIndex()
    {
        $pengirimans = Pengiriman::with(['pesanan.mitra', 'driver.user', 'armada'])->latest()->paginate(10);
        return view('admin.pengiriman.index', compact('pengirimans'));
    }

    public function pengirimanTrack($id)
    {
        $pengiriman = Pengiriman::with(['pesanan.mitra', 'driver.user', 'armada', 'trackings' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('admin.pengiriman.track', compact('pengiriman'));
    }

    public function laporanIndex(Request $request)
    {
        $query = Laporan::with(['pengiriman.pesanan.mitra', 'pengiriman.driver.user', 'pengiriman.armada']);

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        return view('admin.laporan.index', compact('laporans'));
    }

    public function laporanCetak(Request $request)
    {
        $query = Laporan::with(['pengiriman.pesanan.mitra', 'pengiriman.driver.user', 'pengiriman.armada']);

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $laporans = $query->latest()->get();

        return view('admin.laporan.cetak', [
            'laporans' => $laporans,
            'dari' => $request->dari,
            'sampai' => $request->sampai,
        ]);
    }

    public function editPesanan($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        return view('admin.pesanan.edit', compact('pesanan'));
    }

    public function pesananDestroy($id)
    {
        $pesanan = Pesanan::with('pengirimans.armada')->findOrFail($id);

        DB::transaction(function () use ($pesanan) {
            // Kalau ada pengiriman yang masih 'proses', lepas dulu status armada-nya
            // ke 'aktif' supaya armada tidak nyangkut berstatus 'digunakan' selamanya
            // setelah pesanan (dan pengirimannya) dihapus.
            foreach ($pesanan->pengirimans as $pengiriman) {
                if ($pengiriman->status === 'proses' && $pengiriman->armada) {
                    $pengiriman->armada->update(['status' => 'aktif']);
                }
            }

            // Pengiriman, Pembayaran, Tracking, dan Laporan terkait ikut terhapus
            // otomatis lewat cascade delete di database.
            $pesanan->delete();
        });

        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    public function updatePesanan(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_bbm' => 'required|numeric',
            'status' => 'required'
        ]);

        $pesanan = Pesanan::findOrFail($id);

        $pesanan->update([
            'tanggal' => $request->tanggal,
            'jumlah_bbm' => $request->jumlah_bbm,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Pesanan berhasil diperbarui.');
    }
}