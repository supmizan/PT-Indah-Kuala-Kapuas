@extends('layouts.dashboard')

@section('title', 'Mitra Dashboard')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Portal Kemitraan</h2>
        <p class="text-secondary mb-0">Selamat datang kembali. Ajukan dan pantau kebutuhan distribusi BBM Anda di sini.</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('mitra.pesanan.create') }}" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i> Ajukan BBM Online
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
        <div class="summary-card">
            <div class="label">Total Pengajuan</div>
            <div class="value">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="summary-card">
            <div class="label">Menunggu Jadwal</div>
            <div class="value text-danger">{{ $stats['pending_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="summary-card">
            <div class="label">Dalam Perjalanan</div>
            <div class="value text-info">{{ $stats['process_orders'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="summary-card">
            <div class="label">Pesanan Diterima</div>
            <div class="value text-success">{{ $stats['completed_orders'] }}</div>
        </div>
    </div>
</div>

<!-- Recent Orders Card -->
<div class="card-custom">
    <h5 class="fw-bold mb-4"><i class="fa-solid fa-history text-primary me-2"></i> Riwayat Pengajuan Terbaru</h5>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="100">ID Order</th>
                    <th>Rencana Tanggal</th>
                    <th>Volume BBM</th>
                    <th>Status Pengajuan</th>
                    <th width="150" class="text-center">Aksi Lacak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ date('d-m-Y', strtotime($order->tanggal)) }}</td>
                        <td><strong>{{ number_format($order->jumlah_bbm) }} Liter</strong></td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-danger px-3 py-2 text-uppercase">Pending</span>
                            @elseif($order->status === 'diproses')
                                <span class="badge bg-info px-3 py-2 text-uppercase">Dalam Perjalanan</span>
                            @else
                                <span class="badge bg-success px-3 py-2 text-uppercase">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($order->status === 'diproses')
                                @php
                                    $active_del = $order->pengirimans->where('status', 'proses')->first();
                                @endphp
                                @if($active_del)
                                    <a href="{{ route('mitra.pengiriman.track', $active_del->id) }}" class="btn btn-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-map-location-dot"></i> Lacak Live
                                    </a>
                                @else
                                    <span class="text-muted small">Menyiapkan Rute</span>
                                @endif
                            @elseif($order->status === 'selesai')
                                <span class="text-muted small"><i class="fa-solid fa-circle-check text-success"></i> Tiba di Lokasi</span>
                            @else
                                <span class="text-muted small">Menunggu Penugasan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada pengajuan pesanan BBM.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
