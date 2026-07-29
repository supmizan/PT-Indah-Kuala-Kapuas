@extends('layouts.dashboard')

@section('title', 'Manajemen Armada')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Manajemen Armada</h2>
        <p class="text-secondary mb-0">Data seluruh armada PT Indah Kuala Kapuas</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.armada.create') }}" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i> Tambah Armada
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="summary-card">
            <div class="label">Total Armada</div>
            <div class="value">{{ $total_armada }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card">
            <div class="label">Armada Aktif</div>
            <div class="value text-success">{{ $aktif_armada }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="summary-card">
            <div class="label">Maintenance</div>
            <div class="value text-warning">{{ $maintenance_armada }}</div>
        </div>
    </div>
</div>

<!-- Data Table Card -->
<div class="card-custom">
    <h5 class="fw-bold mb-4">Data Armada</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Kode Armada</th>
                    <th>Nomor Polisi</th>
                    <th>Jenis Kendaraan</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($armadas as $index => $armada)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $armada->kode_armada ?? '-' }}</strong></td>
                    <td>{{ $armada->no_polisi }}</td>
                    <td>{{ $armada->jenis }}</td>
                    <td>{{ number_format($armada->kapasitas) }} Liter</td>
                    <td>
                        @if($armada->status === 'aktif')
                        <span class="badge bg-success px-3 py-2 text-uppercase">Aktif</span>
                        @elseif($armada->status === 'maintenance')
                        <span class="badge bg-warning text-dark px-3 py-2 text-uppercase">Maintenance</span>
                        @else
                        <span class="badge bg-info px-3 py-2 text-uppercase">Digunakan</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-2">
                            <a href="{{ route('admin.armada.edit', $armada->id) }}" class="btn btn-warning btn-sm text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            <form action="{{ route('admin.armada.destroy', $armada->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada ini?')" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data armada tangki.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($armadas->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $armadas->links() }}
    </div>
    @endif
</div>
@endsection