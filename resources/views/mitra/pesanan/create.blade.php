@extends('layouts.dashboard')

@section('title', 'Buat Pengajuan BBM')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Ajukan Kebutuhan BBM</h3>
            <a href="{{ route('mitra.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
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

            <form action="{{ route('mitra.pesanan.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-file-signature me-2"></i> Formulir Pengisian</h5>
                
                <div class="mb-3">
                    <label for="jumlah_bbm" class="form-label fw-semibold">Jumlah / Volume BBM (Liter)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-fill-drip text-secondary"></i></span>
                        <input type="number" name="jumlah_bbm" id="jumlah_bbm" class="form-control" placeholder="Contoh: 5000" min="1000" max="100000" value="{{ old('jumlah_bbm', 5000) }}" required>
                        <span class="input-group-text bg-light">LITER</span>
                    </div>
                    <small class="text-muted">Minimum pengajuan adalah 1.000 Liter.</small>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="form-label fw-semibold">Rencana Tanggal Pengiriman</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-days text-secondary"></i></span>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Kirim Pengajuan BBM <i class="fa-solid fa-paper-plane ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
