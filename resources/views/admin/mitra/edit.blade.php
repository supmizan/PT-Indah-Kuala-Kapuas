@extends('layouts.dashboard')

@section('title', 'Edit Mitra')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Edit Data Mitra</h3>
            <a href="{{ route('admin.mitra.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
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

            <form action="{{ route('admin.mitra.update', $mitra->id) }}" method="POST">
                @csrf

                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-circle-user me-2"></i> Akun Pengguna</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Nama Penanggung Jawab</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $mitra->user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $mitra->user->email) }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="form-label fw-medium">Password Baru (Biarkan kosong jika tidak ingin diubah)</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-building me-2"></i> Profil Perusahaan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nama_perusahaan" class="form-label fw-medium">Nama Perusahaan / Instansi</label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $mitra->nama_perusahaan) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label fw-medium">Nomor HP / Kontak</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp', $mitra->no_hp) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga_per_liter" class="form-label fw-medium">Harga BBM per Liter (Rp)</label>
                        <input type="number" step="0.01" min="0" name="harga_per_liter" id="harga_per_liter" class="form-control" value="{{ old('harga_per_liter', $mitra->harga_per_liter) }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="alamat" class="form-label fw-medium">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" class="form-control" required>{{ old('alamat', $mitra->alamat) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label fw-medium">Latitude <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Contoh: -0.0270" value="{{ old('latitude', $mitra->latitude) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label fw-medium">Longitude <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Contoh: 109.3500" value="{{ old('longitude', $mitra->longitude) }}">
                        <small class="text-muted">
                            Cara ambil koordinat: buka Google Maps &rarr; klik kanan di titik lokasi mitra &rarr; klik angka koordinat yang muncul untuk copy.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Simpan Perubahan <i class="fa-solid fa-save ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection