@extends('layouts.dashboard')

@section('title', 'Pembayaran Pesanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Pembayaran Pesanan #{{ $pesanan->id }}</h3>
            <a href="{{ route('mitra.pesanan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-2xs mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-2xs mb-4">{{ session('error') }}</div>
        @endif

        <div class="card-custom">
            <table class="table table-borderless mb-4">
                <tr>
                    <td class="text-muted" style="width: 200px;">Jumlah BBM</td>
                    <td>: {{ number_format($pesanan->jumlah_bbm) }} liter</td>
                </tr>
                <tr>
                    <td class="text-muted">Tanggal Pengangkutan</td>
                    <td>: {{ \Carbon\Carbon::parse($pesanan->tanggal)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Total Tagihan</td>
                    <td>: <span class="fw-bold fs-5">Rp{{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}</span></td>
                </tr>
                <tr>
                    <td class="text-muted">Status Pembayaran</td>
                    <td>:
                        @if($pembayaran->status === 'lunas')
                        <span class="badge bg-success">Lunas</span>
                        @elseif($pembayaran->status === 'menunggu_verifikasi')
                        <span class="badge bg-info text-dark">Menunggu Verifikasi Admin</span>
                        @elseif($pembayaran->status === 'ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                        @else
                        <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                        @endif
                    </td>
                </tr>
            </table>

            @if($pembayaran->status === 'lunas')
            {{-- Sudah lunas, tidak perlu apa-apa lagi --}}
            <div class="alert alert-success mb-0">
                <i class="fa-solid fa-circle-check me-1"></i> Pembayaran sudah dikonfirmasi lunas oleh admin.
            </div>

            @elseif($pembayaran->status === 'menunggu_verifikasi')
            {{-- Sudah upload, tinggal nunggu admin --}}
            <div class="alert alert-info mb-3">
                <i class="fa-solid fa-clock me-1"></i> Bukti transfer Anda sedang diverifikasi oleh admin. Mohon tunggu.
            </div>
            @if($pembayaran->bukti_transfer)
            <p class="text-muted small mb-1">Bukti yang sudah Anda upload:</p>
            <img src="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}" class="img-fluid rounded-3 border" style="max-height: 300px;" alt="Bukti transfer">
            @endif

            @else
            {{-- status: menunggu (belum upload) atau ditolak (upload ulang) --}}
            @if($pembayaran->status === 'ditolak')
            <div class="alert alert-danger mb-3">
                <i class="fa-solid fa-circle-exclamation me-1"></i>
                Bukti transfer sebelumnya ditolak admin.
                @if($pembayaran->catatan_admin)
                <br>Alasan: {{ $pembayaran->catatan_admin }}
                @endif
                Silakan upload ulang bukti transfer yang benar.
            </div>
            @endif

            <div class="text-center mb-4">
                <p class="fw-semibold mb-2">Scan QRIS berikut untuk membayar:</p>

                <ul class="nav nav-pills justify-content-center gap-2 mb-3" id="qris-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="qris1-tab" data-bs-toggle="tab" data-bs-target="#qris1-pane" type="button" role="tab" aria-controls="qris1-pane" aria-selected="true">
                            QRIS (DANA)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qris2-tab" data-bs-toggle="tab" data-bs-target="#qris2-pane" type="button" role="tab" aria-controls="qris2-pane" aria-selected="false">
                            QRIS (ShopeePay)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="qris-tab-content">
                    <div class="tab-pane fade show active" id="qris1-pane" role="tabpanel" aria-labelledby="qris1-tab">
                        <img src="{{ asset('images/qris.jpeg') }}" alt="QRIS DANA - PT Indah Kuala Kapuas" class="img-fluid rounded-3 border p-2" style="max-width: 320px;">
                    </div>
                    <div class="tab-pane fade" id="qris2-pane" role="tabpanel" aria-labelledby="qris2-tab">
                        <img src="{{ asset('images/qris2.jpeg') }}" alt="QRIS ShopeePay - PT Indah Kuala Kapuas" class="img-fluid rounded-3 border p-2" style="max-width: 320px;">
                    </div>
                </div>

                <p class="text-muted small mt-2 mb-0">
                    Setelah transfer, screenshot bukti pembayaran (menampilkan nominal & status berhasil), lalu upload lewat form di bawah ini.
                </p>
            </div>

            <form action="{{ route('mitra.pembayaran.upload', $pesanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="bukti_transfer" class="form-label fw-medium">Upload Bukti Transfer (JPG/PNG, maks. 2MB)</label>
                    <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control @error('bukti_transfer') is-invalid @enderror" accept="image/png, image/jpeg" required>
                    @error('bukti_transfer')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fa-solid fa-upload me-1"></i> Kirim Bukti Transfer
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection