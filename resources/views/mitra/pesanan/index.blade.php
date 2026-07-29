@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Riwayat Pesanan BBM</h2>
        <p class="text-secondary mb-0">Daftar seluruh riwayat pengajuan pengangkutan BBM Anda</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('mitra.pesanan.create') }}" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i> Ajukan BBM Baru
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="100">ID Order</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Volume BBM</th>
                    <th>Petugas Pengiriman (Driver)</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th width="180" class="text-center">Pelacakan Live</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanans as $order)
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ date('d-m-Y', strtotime($order->tanggal)) }}</td>
                        <td><strong>{{ number_format($order->jumlah_bbm) }} Liter</strong></td>
                        <td>
                            @php
                                $delivery = $order->pengirimans->first();
                            @endphp
                            @if($delivery)
                                <div class="fw-semibold">{{ $delivery->driver->user->name }}</div>
                                <small class="text-muted">No HP: {{ $delivery->driver->no_hp }}</small>
                            @else
                                <span class="text-muted small">Menunggu Jadwal</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-danger px-3 py-2 text-uppercase">Pending</span>
                            @elseif($order->status === 'diproses')
                                <span class="badge bg-info px-3 py-2 text-uppercase">Dalam Perjalanan</span>
                            @else
                                <span class="badge bg-success px-3 py-2 text-uppercase">Selesai</span>
                            @endif
                        </td>
                        <td>
                            @if($order->pembayaran && $order->pembayaran->status === 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @elseif($order->pembayaran && $order->pembayaran->status === 'gagal')
                                <span class="badge bg-danger">Gagal</span>
                                <a href="{{ route('mitra.pembayaran.show', $order->id) }}" class="btn btn-sm btn-outline-primary ms-1">Bayar Ulang</a>
                            @elseif($order->pembayaran && $order->pembayaran->status === 'kedaluwarsa')
                                <span class="badge bg-secondary">Kedaluwarsa</span>
                                <a href="{{ route('mitra.pembayaran.show', $order->id) }}" class="btn btn-sm btn-outline-primary ms-1">Bayar Ulang</a>
                            @else
                                <a href="{{ route('mitra.pembayaran.show', $order->id) }}" class="btn btn-sm btn-warning fw-semibold">Bayar Sekarang</a>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($order->status === 'diproses' && $delivery && $delivery->status === 'proses')
                                <a href="{{ route('mitra.pengiriman.track', $delivery->id) }}" class="btn btn-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-map-location-dot"></i> Lacak Posisi
                                </a>
                            @elseif($order->status === 'selesai')
                                <span class="text-muted small"><i class="fa-solid fa-check-double text-success"></i> BBM Diterima</span>
                            @else
                                <span class="text-muted small">Menunggu Rute Aktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat pesanan BBM.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesanans->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $pesanans->links() }}
        </div>
    @endif
</div>
@endsection
