@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header text-center py-4 border-0" style="background-color: #0066ff; border-bottom: 2px solid #0066ff !important;">
                <img src="{{ asset('logo.jpg') }}" alt="Logo PT IKK" style="width: 70px; height: 70px; object-fit: contain; background-color: #ffffff; padding: 4px; border-radius: 12px; margin-bottom: 12px;">
                <h4 class="fw-bold mb-0 text-white">PT Indah Kuala Kapuas</h4>
                <small class="text-white-50">Sistem Informasi Manajemen Pengangkutan BBM</small>
            </div>
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-center mb-4">Silakan Masuk ke Akun Anda</h5>
                
                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 shadow-2xs mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-secondary"></i></span>
                            <input type="email" name="email" id="email" class="form-control bg-light border-start-0 ps-0" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-medium mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-secondary"></i></span>
                            <input type="password" name="password" id="password" class="form-control bg-light border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                        Masuk <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                    </button>
                </form>
            </div>
            <div class="card-footer bg-light text-center py-3 border-0">
                <span class="small text-secondary"><i class="fa-solid fa-circle-info text-primary me-1"></i> Hubungi Administrator untuk mendaftarkan akun baru atau jika terjadi kendala login.</span>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <div class="p-3 bg-white rounded-3 shadow-sm border">
                <small class="text-secondary d-block fw-semibold mb-2">Akun Demo Pengujian:</small>
                <div class="d-flex flex-column gap-1 text-start">
                    <span class="small">👤 <strong>Admin:</strong> <code>admin@ikk.com</code> / <code>password</code></span>
                    <span class="small">🏢 <strong>Mitra:</strong> <code>borneo@mitra.com</code> / <code>password</code></span>
                    <span class="small">🚚 <strong>Driver:</strong> <code>budi@driver.com</code> / <code>password</code></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
