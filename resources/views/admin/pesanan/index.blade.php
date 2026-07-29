@extends('layouts.dashboard')

@section('title', 'Kelola Pesanan')

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
                        @elseif($pesanan->pembayaran && $pesanan->pembayaran->status === 'gagal')
                        <span class="badge bg-danger">Gagal</span>
                        @elseif($pesanan->pembayaran && $pesanan->pembayaran->status === 'kedaluwarsa')
                        <span class="badge bg-secondary">Kedaluwarsa</span>
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

                                <form action="{{ route('admin.pesanan.destroy', $pesanan->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan #{{ $pesanan->id }} ini? Data pengiriman, pembayaran, dan laporan terkait juga akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.')"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;"
                                        title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>

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