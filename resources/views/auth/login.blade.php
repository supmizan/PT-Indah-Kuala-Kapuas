@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Card Login -->
            <div class="card-header text-center py-4 border-0" style="background-color: #0066ff; border-bottom: 2px solid #0066ff !important;">
                <img src="{{ asset('logo.jpg') }}" alt="Logo PT IKK" style="width: 70px; height: 70px; object-fit: contain; background-color: #ffffff; padding: 4px; border-radius: 12px; margin-bottom: 12px;">
                <h4 class="fw-bold mb-0 text-white">PT Indah Kuala Kapuas</h4>
                <small class="text-white-50">Sistem Informasi Manajemen Pengangkutan BBM</small>
            </div>
            
            <!-- Body Card Form -->
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-center mb-4">Silakan Masuk ke Akun Anda</h5>
                
                <!-- Tampilan Pesan Error Login -->
                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 shadow-2xs mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Input Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-secondary"></i></span>
                            <input type="email" name="email" id="email" class="form-control bg-light border-start-0 ps-0" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>
                    
                    <!-- Input Password + Tautan Lupa Password (Opsi 1) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-medium mb-0">Password</label>
                            <a href="#" class="text-decoration-none small text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Lupa Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-secondary"></i></span>
                            <input type="password" name="password" id="password" class="form-control bg-light border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                        Masuk <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                    </button>
                </form>
            </div>
            
            <div class="card-footer bg-light text-center py-3 border-0">
                <span class="small text-secondary"><i class="fa-solid fa-circle-info text-primary me-1"></i> Hubungi Administrator untuk mendaftarkan akun baru atau jika terjadi kendala login.</span>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL POPUP LUPA PASSWORD (OPSI 1 - WHATSAPP ADMIN PINTAS)
     ======================================================== -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <!-- Header Modal -->
            <div class="modal-header border-0 bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel"><i class="fa-solid fa-circle-question me-2"></i> Lupa Password Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body p-4 text-center">
                <i class="fa-solid fa-user-lock text-primary fs-1 mb-3"></i>
                <h6 class="fw-bold mb-2">Reset Password Melalui Administrator</h6>
                <p class="text-muted small mb-4">
                    Untuk menjaga keamanan data pengangkutan BBM PT IKK, proses reset sandi harus diajukan langsung ke pihak administrator kantor untuk diverifikasi terlebih dahulu.
                </p>
                <!-- Tombol Pintas WhatsApp Web / Mobile -->
                <a href="https://wa.me/6285931148582?text=Halo%20Admin%20PT%20IKK%2C%20saya%20lupa%20password%20untuk%20akun%20SIM%20Pengangkutan%20BBM%20saya.%20Mohon%20bantuan%20reset%20password.%20Email%20saya%3A%20" 
                   target="_blank" 
                   class="btn btn-success w-100 py-2.5 rounded-3 fw-bold shadow-xs">
                    <i class="fa-brands fa-whatsapp me-2"></i> Hubungi Admin via WhatsApp
                </a>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light btn-sm px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
