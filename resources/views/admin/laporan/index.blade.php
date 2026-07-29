@extends('layouts.dashboard')

@section('title', 'Laporan Operasional')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Laporan Hasil Pengiriman</h2>
        <p class="text-secondary mb-0">Daftar laporan operasional serah terima BBM oleh Driver di lapangan</p>
    </div>
</div>

<div class="card-custom mb-3">
    <form action="{{ route('admin.laporan.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            @if(request('dari') || request('sampai'))
            <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
            @endif
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.laporan.cetak', request()->only(['dari', 'sampai'])) }}"
                target="_blank"
                class="btn btn-sm btn-primary">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Mitra Penerima</th>
                    <th>Driver Pelaksana</th>
                    <th>Armada Tangki</th>
                    <th>Tanggal Selesai</th>
                    <th>Catatan / Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $index => $lap)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold text-primary">{{ $lap->pengiriman->pesanan->mitra->nama_perusahaan }}</div>
                        <small class="text-muted">Volume: {{ number_format($lap->pengiriman->pesanan->jumlah_bbm) }} L</small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $lap->pengiriman->driver->user->name }}</div>
                        <small class="text-muted">No HP: {{ $lap->pengiriman->driver->no_hp }}</small>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $lap->pengiriman->armada->no_polisi }}</div>
                        <small class="text-muted">{{ $lap->pengiriman->armada->jenis }}</small>
                    </td>
                    <td>{{ date('d-m-Y H:i', strtotime($lap->created_at)) }}</td>
                    <td>
                        <div class="bg-light p-2.5 rounded-3 border small text-slate-800" style="max-width: 400px; line-height: 1.4;">
                            {{ $lap->keterangan }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada laporan pengiriman yang disubmit.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($laporans->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $laporans->links() }}
    </div>
    @endif
</div>
@endsection