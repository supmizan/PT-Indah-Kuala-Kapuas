<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PembayaranController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Route::get('/cek-password', function () {
    $user = User::where('email', 'admin@ikk.com')->first();

    return [
        'cocok' => Hash::check('password', $user->password),
    ];
});

Route::get('/cek-db', function () {
    return response()->json([
        'database' => DB::connection()->getDatabaseName(),
        'users' => DB::table('users')->count(),
        'email_admin' => DB::table('users')->where('email', 'admin@ikk.com')->first(),
    ]);
});

// Public Routes
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // Fallback GET logout

// Admin Area
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Driver CRUD
    Route::get('/driver', [AdminController::class, 'driverIndex'])->name('driver.index');
    Route::get('/driver/create', [AdminController::class, 'driverCreate'])->name('driver.create');
    Route::post('/driver', [AdminController::class, 'driverStore'])->name('driver.store');
    Route::get('/driver/{id}/edit', [AdminController::class, 'driverEdit'])->name('driver.edit');
    Route::post('/driver/{id}', [AdminController::class, 'driverUpdate'])->name('driver.update');
    Route::delete('/driver/{id}', [AdminController::class, 'driverDestroy'])->name('driver.destroy');

    // Mitra CRUD
    Route::get('/mitra', [AdminController::class, 'mitraIndex'])->name('mitra.index');
    Route::get('/mitra/create', [AdminController::class, 'mitraCreate'])->name('mitra.create');
    Route::post('/mitra', [AdminController::class, 'mitraStore'])->name('mitra.store');
    Route::get('/mitra/{id}/edit', [AdminController::class, 'mitraEdit'])->name('mitra.edit');
    Route::post('/mitra/{id}', [AdminController::class, 'mitraUpdate'])->name('mitra.update');
    Route::delete('/mitra/{id}', [AdminController::class, 'mitraDestroy'])->name('mitra.destroy');

    // Armada CRUD
    Route::get('/armada', [AdminController::class, 'armadaIndex'])->name('armada.index');
    Route::get('/armada/create', [AdminController::class, 'armadaCreate'])->name('armada.create');
    Route::post('/armada', [AdminController::class, 'armadaStore'])->name('armada.store');
    Route::get('/armada/{id}/edit', [AdminController::class, 'armadaEdit'])->name('armada.edit');
    Route::post('/armada/{id}', [AdminController::class, 'armadaUpdate'])->name('armada.update');
    Route::delete('/armada/{id}', [AdminController::class, 'armadaDestroy'])->name('armada.destroy');

    Route::get('/pesanan/{id}/edit', [AdminController::class, 'editPesanan'])
        ->name('pesanan.edit');

    Route::put('/pesanan/{id}', [AdminController::class, 'updatePesanan'])
        ->name('pesanan.update');

    Route::delete('/pesanan/{id}', [AdminController::class, 'pesananDestroy'])
        ->name('pesanan.destroy');
    // Order (Pesanan) Dispatch
    Route::get('/pesanan', [AdminController::class, 'pesananIndex'])->name('pesanan.index');
    Route::get('/pesanan/{id}/dispatch', [AdminController::class, 'pesananDispatchForm'])->name('pesanan.dispatch.form');
    Route::post('/pesanan/{id}/dispatch', [AdminController::class, 'pesananDispatch'])->name('pesanan.dispatch');

    // Delivery (Pengiriman) Tracking & Report
    Route::get('/pengiriman', [AdminController::class, 'pengirimanIndex'])->name('pengiriman.index');
    Route::get('/pengiriman/{id}/track', [AdminController::class, 'pengirimanTrack'])->name('pengiriman.track');
    Route::get('/laporan', [AdminController::class, 'laporanIndex'])->name('laporan.index');
    Route::get('/laporan/cetak', [AdminController::class, 'laporanCetak'])->name('laporan.cetak');
});

// Mitra Area
Route::middleware(['auth', 'role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
    Route::get('/pesanan/buat', [MitraController::class, 'createPesanan'])->name('pesanan.create');
    Route::post('/pesanan/buat', [MitraController::class, 'storePesanan'])->name('pesanan.store');
    Route::get('/pesanan', [MitraController::class, 'pesananIndex'])->name('pesanan.index');
    Route::get('/pengiriman/{id}/track', [MitraController::class, 'trackPengiriman'])->name('pengiriman.track');
    Route::get('/pesanan/{id}/bayar', [PembayaranController::class, 'show'])->name('pembayaran.show');
});

// Webhook Midtrans (dipanggil dari server Midtrans, bukan dari browser user, jadi di luar middleware auth
// dan dikecualikan dari CSRF di bootstrap/app.php)
Route::post('/midtrans/notification', [PembayaranController::class, 'notification'])->name('midtrans.notification');

// Driver Area
Route::middleware(['auth', 'role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::post('/pengiriman/{id}/update-lokasi', [DriverController::class, 'updateLocation'])->name('pengiriman.update-lokasi');
    Route::post('/pengiriman/{id}/selesai', [DriverController::class, 'completePengiriman'])->name('pengiriman.complete');
});

// Live location update JSON endpoint
Route::middleware('auth')->get('/pengiriman/{id}/lokasi-json', function ($id) {
    $trackings = \App\Models\Tracking::where('pengiriman_id', $id)->orderBy('waktu', 'asc')->get();
    return response()->json($trackings);
})->name('pengiriman.lokasi-json');
