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
                        @elseif($pembayaran->status === 'gagal')
                            <span class="badge bg-danger">Gagal</span>
                        @elseif($pembayaran->status === 'kedaluwarsa')
                            <span class="badge bg-secondary">Kedaluwarsa</span>
                        @else
                            <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                        @endif
                    </td>
                </tr>
            </table>

            <button id="btn-bayar" class="btn btn-primary w-100 py-2">
                <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang
            </button>
            <p class="text-muted small mt-3 mb-0">
                Ini menggunakan Midtrans <strong>Sandbox</strong> (mode uji coba) — tidak ada uang sungguhan yang diproses.
                Untuk simulasi pembayaran berhasil, gunakan nomor kartu uji <code>4811 1111 1111 1114</code>, CVV bebas, expiry bebas (tanggal depan).
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
<script>
    document.getElementById('btn-bayar').addEventListener('click', function () {
        snap.pay("{{ $pembayaran->snap_token }}", {
            onSuccess: function () {
                window.location.href = "{{ route('mitra.pesanan.index') }}";
            },
            onPending: function () {
                window.location.href = "{{ route('mitra.pesanan.index') }}";
            },
            onError: function () {
                alert('Pembayaran gagal diproses. Silakan coba lagi.');
            },
            onClose: function () {
                // Pengguna menutup popup tanpa menyelesaikan pembayaran — biarkan tetap di halaman ini.
            }
        });
    });
</script>
@endsection
