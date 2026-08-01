@extends('layouts.dashboard')

@section('title', 'Jadwalkan Pengiriman')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Atur & Jadwalkan Distribusi</h3>
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </div>

        <!-- Order Information Summary Card -->
        <div class="card-custom bg-primary text-white mb-4">
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-invoice me-2"></i> Rincian Pengajuan Pesanan #{{ $pesanan->id }}</h5>
            <div class="row g-3">
                <div class="col-6">
                    <small class="text-white-50 d-block">Nama Perusahaan Mitra</small>
                    <strong>{{ $pesanan->mitra->nama_perusahaan }}</strong>
                </div>
                <div class="col-6">
                    <small class="text-white-50 d-block">Kontak Telepon / HP</small>
                    <strong>{{ $pesanan->mitra->no_hp }}</strong>
                </div>
                <div class="col-6">
                    <small class="text-white-50 d-block">Volume BBM Pengajuan</small>
                    <strong class="fs-5">{{ number_format($pesanan->jumlah_bbm) }} Liter</strong>
                </div>
                <div class="col-6">
                    <small class="text-white-50 d-block">Tanggal Pengajuan</small>
                    <strong>{{ date('d-m-Y', strtotime($pesanan->tanggal)) }}</strong>
                </div>
                <div class="col-12">
                    <small class="text-white-50 d-block">Alamat Tujuan Pengiriman</small>
                    <strong>{{ $pesanan->mitra->alamat }}</strong>
                </div>
            </div>
        </div>

        <!-- Dispatch Settings Card -->
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

            <form action="{{ route('admin.pesanan.dispatch', $pesanan->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="driver_id" class="form-label fw-semibold">Pilih Pengemudi (Driver Aktif)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user text-secondary"></i></span>
                        <select name="driver_id" id="driver_id" class="form-select" required>
                            <option value="">-- Pilih Driver --</option>
                            @foreach($drivers as $driver)
                            @php $sedangBertugas = in_array($driver->id, $busyDriverIds); @endphp
                            <option value="{{ $driver->id }}" @disabled($sedangBertugas)>
                                {{ $driver->user->name }} (Telp: {{ $driver->no_hp }})
                                @if($sedangBertugas) — Sedang Dalam Perjalanan @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="armada_id" class="form-label fw-semibold">Pilih Kendaraan (Armada Tersedia)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-truck text-secondary"></i></span>
                        <select name="armada_id" id="armada_id" class="form-select" required>
                            <option value="">-- Pilih Armada --</option>
                            @foreach($armadas as $armada)
                            <option value="{{ $armada->id }}">
                                {{ $armada->no_polisi }} - {{ $armada->jenis }} (Kapasitas: {{ number_format($armada->kapasitas) }} L)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Pastikan kapasitas mobil tangki mencukupi volume pesanan.</small>
                </div>

                <div class="mb-4">
                    <label for="tanggal_kirim" class="form-label fw-semibold">Tanggal Rencana Kirim</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-days text-secondary"></i></span>
                        <input type="date" name="tanggal_kirim" id="tanggal_kirim" class="form-control" value="{{ $pesanan->tanggal }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Jadwalkan & Kirim Tugas <i class="fa-solid fa-paper-plane ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection