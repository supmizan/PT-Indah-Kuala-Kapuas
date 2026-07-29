@extends('layouts.dashboard')

@section('title', 'Pemantauan Pengiriman')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Manajemen Pengiriman</h2>
        <p class="text-secondary mb-0">Pemantauan perjalanan armada pengangkutan BBM secara real-time</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Mitra Penerima</th>
                    <th>Driver Pengoperasi</th>
                    <th>Armada Tangki</th>
                    <th>Tanggal Kirim</th>
                    <th>Status Perjalanan</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengirimans as $index => $del)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-primary">{{ $del->pesanan->mitra->nama_perusahaan }}</div>
                            <small class="text-muted">Volume: {{ number_format($del->pesanan->jumlah_bbm) }} L</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $del->driver->user->name }}</div>
                            <small class="text-muted">No HP: {{ $del->driver->no_hp }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $del->armada->no_polisi }}</div>
                            <small class="text-muted">{{ $del->armada->jenis }}</small>
                        </td>
                        <td>{{ date('d-m-Y', strtotime($del->tanggal_kirim)) }}</td>
                        <td>
                            @if($del->status === 'proses')
                                <span class="badge bg-info px-3 py-2 text-uppercase">Dalam Perjalanan</span>
                            @else
                                <span class="badge bg-success px-3 py-2 text-uppercase">Tiba di Lokasi</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($del->status === 'proses')
                                <a href="{{ route('admin.pengiriman.track', $del->id) }}" class="btn btn-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-map-location-dot"></i> Lacak Live
                                </a>
                            @else
                                <span class="text-muted small">Tiba Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada jadwal operasional pengiriman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pengirimans->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $pengirimans->links() }}
        </div>
    @endif
</div>
@endsection
