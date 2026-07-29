@extends('layouts.dashboard')

@section('title', 'Tambah Armada')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Tambah Armada Baru</h3>
            <a href="{{ route('admin.armada.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </div>

        <div class="card-custom">
            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 shadow-2xs mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.armada.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-truck-moving me-2"></i> Identitas Kendaraan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="kode_armada" class="form-label fw-medium">Kode Armada</label>
                        <input type="text" name="kode_armada" id="kode_armada" class="form-control" placeholder="Contoh: ARM-001" value="{{ old('kode_armada') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_polisi" class="form-label fw-medium">Nomor Polisi (Plat Nomor)</label>
                        <input type="text" name="no_polisi" id="no_polisi" class="form-control" placeholder="Contoh: KB 1234 AA" value="{{ old('no_polisi') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis" class="form-label fw-medium">Jenis Mobil Tangki</label>
                        <input type="text" name="jenis" id="jenis" class="form-control" placeholder="Contoh: Tangki Hino / Fuso" value="{{ old('jenis') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="kapasitas" class="form-label fw-medium">Kapasitas Tangki (Liter)</label>
                        <input type="number" name="kapasitas" id="kapasitas" class="form-control" placeholder="Contoh: 8000" value="{{ old('kapasitas') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status Awal</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif (Tersedia)</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="digunakan" {{ old('status') === 'digunakan' ? 'selected' : '' }}>Digunakan (Dalam Perjalanan)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Simpan Armada Baru <i class="fa-solid fa-save ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection