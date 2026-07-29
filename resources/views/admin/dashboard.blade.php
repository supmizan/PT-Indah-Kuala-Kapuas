@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Dashboard Administrator</h2>
        <p class="text-secondary mb-0">Selamat datang kembali di panel administrasi PT Indah Kuala Kapuas.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="summary-card">
            <div class="label">Mitra Kerja</div>
            <div class="value">{{ $stats['mitras'] }}</div>
            <a href="{{ route('admin.mitra.index') }}" class="small text-decoration-none mt-2">Lihat Detail &rarr;</a>
        </div>
    </div>
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="summary-card">
            <div class="label">Driver (Supir)</div>
            <div class="value text-success">{{ $stats['drivers'] }}</div>
            <a href="{{ route('admin.driver.index') }}" class="small text-decoration-none text-success mt-2">Lihat Detail &rarr;</a>
        </div>
    </div>
    <div class="col-md-4 col-lg-2.4 col-sm-6">
        <div class="summary-card">
            <div class="label">Armada Tangki</div>
            <div class="value text-warning">{{ $stats['armadas'] }}</div>
            <a href="{{ route('admin.armada.index') }}" class="small text-decoration-none text-warning mt-2">Lihat Detail &rarr;</a>
        </div>
    </div>
    <div class="col-md-6 col-lg-2.4 col-sm-6">
        <div class="summary-card">
            <div class="label">Pengiriman Aktif</div>
            <div class="value text-info">{{ $stats['active_deliveries'] }}</div>
            <a href="{{ route('admin.pengiriman.index') }}" class="small text-decoration-none text-info mt-2">Pantau Peta &rarr;</a>
        </div>
    </div>
    <div class="col-md-6 col-lg-2.4 col-sm-12">
        <div class="summary-card">
            <div class="label">Order Pending</div>
            <div class="value text-danger">{{ $stats['pending_orders'] }}</div>
            <a href="{{ route('admin.pesanan.index') }}" class="small text-decoration-none text-danger mt-2">Atur Jadwal &rarr;</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card-custom">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-bolt text-warning me-2"></i>Aksi Cepat</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('admin.driver.create') }}" class="btn btn-outline-primary text-start d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Tambah Driver Baru
                </a>
                <a href="{{ route('admin.mitra.create') }}" class="btn btn-outline-success text-start d-flex align-items-center gap-2">
                    <i class="fa-solid fa-building-circle-add"></i> Tambah Mitra Baru
                </a>
                <a href="{{ route('admin.armada.create') }}" class="btn btn-outline-warning text-dark text-start d-flex align-items-center gap-2">
                    <i class="fa-solid fa-truck"></i> Tambah Armada Baru
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-lines"></i> Lihat Laporan Operasional
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Deliveries -->
    <div class="col-lg-8">
        <div class="card-custom">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-history me-2 text-primary"></i> Pengiriman Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Mitra Penerima</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_deliveries as $del)
                            <tr>
                                <td><strong>#{{ $del->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $del->pesanan->mitra->nama_perusahaan }}</div>
                                    <small class="text-muted">{{ number_format($del->pesanan->jumlah_bbm) }} L</small>
                                </td>
                                <td>{{ $del->driver->user->name }}</td>
                                <td>
                                    @if($del->status === 'proses')
                                        <span class="badge bg-info-subtle text-info px-2 py-1"><i class="fa-solid fa-spinner fa-spin me-1"></i> Pengiriman</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-check me-1"></i> Tiba</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($del->status === 'proses')
                                        <a href="{{ route('admin.pengiriman.track', $del->id) }}" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-map-location-dot"></i> Track</a>
                                    @else
                                        <span class="text-muted small">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pengiriman terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
