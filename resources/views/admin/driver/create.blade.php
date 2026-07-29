@extends('layouts.dashboard')

@section('title', 'Tambah Driver')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Tambah Driver Baru</h3>
            <a href="{{ route('admin.driver.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
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

            <form action="{{ route('admin.driver.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-circle-user me-2"></i> Akun Pengemudi</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Budi Santoso" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="budi@driver.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="form-label fw-medium">Password Akun</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password (minimal 6 karakter)" required>
                    </div>
                </div>

                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-address-card me-2"></i> Profil & Kontak</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label for="no_hp" class="form-label fw-medium">Nomor HP / Kontak</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="0852xxxxxxxx" value="{{ old('no_hp') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="alamat" class="form-label fw-medium">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" class="form-control" placeholder="Jl. Paris 2, Pontianak" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Simpan Driver Baru <i class="fa-solid fa-save ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
