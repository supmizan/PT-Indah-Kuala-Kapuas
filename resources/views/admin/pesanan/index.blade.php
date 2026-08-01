@extends('layouts.dashboard')

@section('title', 'Kelola Pesanan')

@section('styles')
<style>
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-confirm-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 26px;
    }

    .modal-confirm-icon.is-success {
        background-color: #e6f0ff;
        color: #0066ff;
    }

    .modal-confirm-icon.is-danger {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .modal-header {
        border-bottom: none;
        padding-bottom: 0;
    }

    .modal-footer {
        border-top: none;
        padding-top: 0;
    }

    .btn-modal-primary {
        background-color: #0066ff;
        border-color: #0066ff;
        color: #fff;
        border-radius: 8px;
    }

    .btn-modal-primary:hover {
        background-color: #0052cc;
        border-color: #0052cc;
        color: #fff;
    }

    .btn-modal-outline {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background-color: #fff;
        color: #4b5563;
    }

    .btn-modal-outline:hover {
        background-color: #f3f4f6;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Manajemen Pesanan BBM</h2>
        <p class="text-secondary mb-0">Daftar pemesanan BBM masuk dan penugasan jadwal pengiriman</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Mitra Kerja</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Volume BBM</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th width="170" class="text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanans as $index => $pesanan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $pesanan->mitra->nama_perusahaan }}</div>
                        <small class="text-muted">Kontak: {{ $pesanan->mitra->no_hp }}</small>
                    </td>
                    <td>{{ date('d-m-Y', strtotime($pesanan->tanggal)) }}</td>
                    <td><strong>{{ number_format($pesanan->jumlah_bbm) }} Liter</strong></td>
                    <td>
                        @if($pesanan->status === 'pending')
                        <span class="badge bg-danger px-3 py-2">PENDING</span>
                        @elseif($pesanan->status === 'diproses')
                        <span class="badge bg-info px-3 py-2">DIPROSES</span>
                        @else
                        <span class="badge bg-success px-3 py-2">SELESAI</span>
                        @endif
                    </td>
                    <td>
                        @if($pesanan->sudahLunas())
                        <span class="badge bg-success">Lunas</span>
                        @elseif($pesanan->pembayaran && $pesanan->pembayaran->status === 'menunggu_verifikasi')
                        <span class="badge bg-info text-dark d-block mb-1">Menunggu Verifikasi</span>
                        <div class="d-flex flex-column gap-1">
                            <a href="{{ route('admin.pembayaran.bukti', $pesanan->pembayaran->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-image me-1"></i> Lihat Bukti
                            </a>
                            <button type="button" class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#lunasModal{{ $pesanan->id }}">
                                <i class="fa-solid fa-check me-1"></i> Konfirmasi Lunas
                            </button>

                            <div class="modal fade" id="lunasModal{{ $pesanan->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center px-4 pb-2">
                                            <div class="modal-confirm-icon is-success">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </div>
                                            <h5 class="fw-bold mb-2">Konfirmasi Pembayaran</h5>
                                            <p class="text-secondary mb-0">
                                                Tandai pesanan <strong>#{{ $pesanan->id }}</strong> dari <strong>{{ $pesanan->mitra->nama_perusahaan }}</strong> sebagai <strong>lunas</strong>?
                                                Admin dapat menjadwalkan pengiriman setelah ini.
                                            </p>
                                        </div>
                                        <div class="modal-footer justify-content-center px-4 pb-4">
                                            <button type="button" class="btn btn-modal-outline flex-fill" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('admin.pembayaran.verifikasi', $pesanan->pembayaran->id) }}" method="POST" class="flex-fill">
                                                @csrf
                                                <button type="submit" class="btn btn-modal-primary w-100">
                                                    <i class="fa-solid fa-check me-1"></i> Ya, Konfirmasi Lunas
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $pesanan->id }}">
                                <i class="fa-solid fa-xmark me-1"></i> Tolak
                            </button>

                            <div class="modal fade" id="tolakModal{{ $pesanan->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.pembayaran.tolak', $pesanan->pembayaran->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center px-4 pb-2">
                                                <div class="modal-confirm-icon is-danger">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Tolak Bukti Transfer</h5>
                                                <p class="text-secondary mb-3">
                                                    Pesanan <strong>#{{ $pesanan->id }}</strong> dari <strong>{{ $pesanan->mitra->nama_perusahaan }}</strong> akan diminta upload ulang bukti transfer.
                                                </p>
                                                <div class="text-start">
                                                    <label class="form-label small text-secondary">Alasan penolakan (opsional)</label>
                                                    <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: nominal tidak sesuai / gambar buram"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-center px-4 pb-4">
                                                <button type="button" class="btn btn-modal-outline flex-fill" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger flex-fill" style="border-radius: 8px;">
                                                    <i class="fa-solid fa-xmark me-1"></i> Tolak Bukti
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @elseif($pesanan->pembayaran && $pesanan->pembayaran->status === 'ditolak')
                        <span class="badge bg-danger">Ditolak, Menunggu Upload Ulang</span>
                        @else
                        <span class="badge bg-warning text-dark">Menunggu</span>
                        @endif
                    </td>
                    <td class="text-center">

                        <div class="d-flex flex-column align-items-center gap-2">

                            @if($pesanan->status == 'pending' && $pesanan->sudahLunas())

                            <a href="{{ route('admin.pesanan.dispatch.form',$pesanan->id) }}"
                                class="btn btn-primary btn-sm w-100">
                                <i class="fa-solid fa-calendar-check"></i> Jadwalkan
                            </a>

                            @elseif($pesanan->status == 'pending')

                            <span class="badge bg-secondary w-100 py-2">Menunggu Pembayaran Mitra</span>

                            @else

                            <span class="badge bg-success w-100 py-2">Sudah Dijadwalkan</span>

                            @endif

                            <div class="d-inline-flex gap-2">

                                <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}"
                                    class="btn btn-warning btn-sm text-dark d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;"
                                    title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>

                                <button type="button"
                                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;"
                                    title="Hapus"
                                    data-bs-toggle="modal"
                                    data-bs-target="#hapusModal{{ $pesanan->id }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                                <div class="modal fade" id="hapusModal{{ $pesanan->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center px-4 pb-2">
                                                <div class="modal-confirm-icon is-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Hapus Pesanan?</h5>
                                                <p class="text-secondary mb-0">
                                                    Pesanan <strong>#{{ $pesanan->id }}</strong> beserta data pengiriman, pembayaran, dan laporan terkait akan ikut terhapus.
                                                    <strong class="text-danger">Tindakan ini tidak bisa dibatalkan.</strong>
                                                </p>
                                            </div>
                                            <div class="modal-footer justify-content-center px-4 pb-4">
                                                <button type="button" class="btn btn-modal-outline flex-fill" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}" method="POST" class="flex-fill">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger w-100" style="border-radius: 8px;">
                                                        <i class="fa-solid fa-trash me-1"></i> Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan pesanan BBM dari mitra.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pesanans->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $pesanans->links() }}
    </div>
    @endif
</div>
@endsection