@extends('layouts.dashboard')

@section('title', 'Kelola Driver')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Manajemen Driver</h2>
        <p class="text-secondary mb-0">Kelola data pengemudi operasional armada tangki PT Indah Kuala Kapuas</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.driver.create') }}" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i> Tambah Driver
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Nama Pengemudi</th>
                    <th>Email Login</th>
                    <th>No HP / Kontak</th>
                    <th>Alamat Rumah</th>
                    <th>Status</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $index => $driver)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $driver->user->name }}</strong></td>
                        <td>{{ $driver->user->email }}</td>
                        <td>{{ $driver->no_hp }}</td>
                        <td>{{ Str::limit($driver->alamat, 40) }}</td>
                        <td>
                            @if($driver->status === 'aktif')
                                <span class="badge bg-success px-3 py-2">AKTIF</span>
                            @else
                                <span class="badge bg-danger px-3 py-2">NONAKTIF</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.driver.edit', $driver->id) }}" class="btn btn-warning btn-sm text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <form action="{{ route('admin.driver.destroy', $driver->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus driver ini?')" class="m-0">
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
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data pengemudi (driver).</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($drivers->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $drivers->links() }}
        </div>
    @endif
</div>
@endsection
