@extends('layouts.dashboard')

@section('title', 'Kelola Mitra')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Manajemen Mitra Kerja</h2>
        <p class="text-secondary mb-0">Daftar badan usaha penerima distribusi BBM PT Indah Kuala Kapuas</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.mitra.create') }}" class="btn btn-primary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-plus"></i> Tambah Mitra
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Nama Perusahaan</th>
                    <th>Penanggung Jawab</th>
                    <th>Email Login</th>
                    <th>No HP / Kontak</th>
                    <th>Alamat Perusahaan</th>
                    <th width="150" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mitras as $index => $mitra)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $mitra->nama_perusahaan }}</strong></td>
                        <td>{{ $mitra->user->name }}</td>
                        <td>{{ $mitra->user->email }}</td>
                        <td>{{ $mitra->no_hp }}</td>
                        <td>{{ Str::limit($mitra->alamat, 40) }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.mitra.edit', $mitra->id) }}" class="btn btn-warning btn-sm text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <form action="{{ route('admin.mitra.destroy', $mitra->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')" class="m-0">
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
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data mitra kerja.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mitras->hasPages())
        <div class="d-flex justify-content-end mt-4">
            {{ $mitras->links() }}
        </div>
    @endif
</div>
@endsection
