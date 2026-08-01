@extends('layouts.dashboard')

@section('title', 'Dashboard Driver')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Portal Pengemudi (Driver)</h2>
        <p class="text-secondary mb-0">Kelola rute pengiriman dan perbarui status lokasi armada Anda</p>
    </div>
</div>

@if($active_delivery)
<!-- Active Delivery Task Card -->
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="card-custom border-start border-primary border-4">
            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-truck-moving me-2"></i> Tugas Pengiriman Aktif</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <small class="text-muted d-block fw-semibold">Mitra Kerja Penerima</small>
                    <strong class="fs-6">{{ $active_delivery->pesanan->mitra->nama_perusahaan }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block fw-semibold">Kontak Telepon</small>
                    <strong>{{ $active_delivery->pesanan->mitra->no_hp }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block fw-semibold">Volume BBM</small>
                    <strong class="fs-5 text-primary">{{ number_format($active_delivery->pesanan->jumlah_bbm) }} L</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block fw-semibold">Armada Mobil Tangki</small>
                    <strong>{{ $active_delivery->armada->no_polisi }} ({{ $active_delivery->armada->jenis }})</strong>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block fw-semibold">Alamat Pengantaran</small>
                    <p class="mb-0 text-slate-700 bg-light p-2.5 rounded-3 small border"><i class="fa-solid fa-map-marker-alt text-danger me-1"></i> {{ $active_delivery->pesanan->mitra->alamat }}</p>
                </div>
            </div>

            <!-- Live GPS Tracking Simulator -->
            <div class="bg-primary-subtle p-3.5 rounded-4 border border-primary-subtle mb-4">
                <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-satellite-dish me-2"></i> Simulator GPS Geolocation</h6>
                <p class="small text-secondary mb-3">Untuk keperluan demo, klik tombol di bawah untuk menyimulasikan pergerakan mobil tangki dari HQ ke lokasi mitra:</p>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="simulateLocation(-0.0234, 109.3719, 'HQ PT IKK')">1. Start (HQ PT IKK)</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="simulateLocation(-0.0247, 109.3425, 'Tanjungpura')">2. Jalan (Tanjungpura)</button>
                    @php
                    $mitraLoc = $active_delivery->pesanan->mitra;
                    @endphp
                    @if($mitraLoc && $mitraLoc->latitude && $mitraLoc->longitude)
                    <button type="button" class="btn btn-sm btn-primary" onclick="simulateLocation({{ $mitraLoc->latitude }}, {{ $mitraLoc->longitude }}, 'Tiba di Mitra')">3. Finish (Lokasi Mitra)</button>
                    @else
                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Koordinat mitra ini belum diisi admin">3. Finish (Lokasi Mitra)</button>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between p-2.5 bg-white rounded-3 border">
                    <span class="small text-muted" id="gps-status">GPS Siap...</span>
                    <button type="button" class="btn btn-sm btn-success" onclick="shareCurrentLocation()"><i class="fa-solid fa-location-crosshairs me-1"></i> Kirim GPS Asli</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Finish Task Form -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-circle-check me-2"></i> Konfirmasi Selesai Pengantaran</h5>
            <p class="text-secondary small mb-4">
                Jika Bahan Bakar Minyak (BBM) telah berhasil diserahterimakan ke tangki penyimpanan mitra, silakan tulis catatan serah terima di bawah untuk menyelesaikan tugas.
            </p>

            <form action="{{ route('driver.pengiriman.complete', $active_delivery->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="keterangan" class="form-label fw-semibold">Catatan Pengiriman / Keterangan Laporan</label>
                    <textarea name="keterangan" id="keterangan" rows="5" class="form-control" placeholder="Contoh: BBM 8.000 Liter telah dimasukkan ke tangki utama PT Borneo Logistik. Segel aman dan dokumen serah terima telah ditandatangani." required></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 rounded-3 fw-bold text-uppercase shadow-sm">
                    Selesaikan Tugas & Kirim Laporan <i class="fa-solid fa-circle-check ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@else
<!-- No Active Task Banner -->
<div class="card-custom bg-white border text-center py-5 mb-5">
    <i class="fa-solid fa-clipboard-check fs-1 text-muted mb-3"></i>
    <h4 class="fw-bold">Tidak Ada Tugas Aktif</h4>
    <p class="text-secondary mb-0">Saat ini Anda tidak memiliki jadwal pengangkutan BBM yang sedang aktif. Silakan tunggu jadwal dari administrator.</p>
</div>
@endif

<!-- History Table -->
<div class="card-custom">
    <h5 class="fw-bold mb-4"><i class="fa-solid fa-history me-2 text-primary"></i> Riwayat Tugas Pengiriman</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="80">No</th>
                    <th>Mitra Kerja</th>
                    <th>Tanggal Kirim</th>
                    <th>Volume BBM</th>
                    <th>Armada Mobil Tangki</th>
                    <th>Status Tugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $index => $del)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $del->pesanan->mitra->nama_perusahaan }}</div>
                        <small class="text-muted">Alamat: {{ Str::limit($del->pesanan->mitra->alamat, 40) }}</small>
                    </td>
                    <td>{{ date('d-m-Y', strtotime($del->tanggal_kirim)) }}</td>
                    <td><strong>{{ number_format($del->pesanan->jumlah_bbm) }} Liter</strong></td>
                    <td>
                        <div class="fw-semibold">{{ $del->armada->no_polisi }}</div>
                        <small class="text-muted">{{ $del->armada->jenis }}</small>
                    </td>
                    <td>
                        @if($del->status === 'proses')
                        <span class="badge bg-info px-3 py-2 text-uppercase">Dalam Proses</span>
                        @else
                        <span class="badge bg-success px-3 py-2 text-uppercase">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat tugas pengiriman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($deliveries->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $deliveries->links() }}
    </div>
    @endif
</div>
@endsection

@section('scripts')
@if($active_delivery)
<script>
    // Send coordinates via AJAX
    function sendCoordinates(lat, lng, label) {
        const url = "{{ route('driver.pengiriman.update-lokasi', $active_delivery->id) }}";
        const statusElement = document.getElementById('gps-status');

        statusElement.innerHTML = `<span class="spinner-border spinner-border-sm text-primary me-1" role="status"></span> Mengirim ${label}...`;

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    statusElement.innerHTML = `<i class="fa-solid fa-circle-check text-success me-1"></i> Posisi: ${label} (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                } else {
                    statusElement.innerHTML = `<i class="fa-solid fa-circle-xmark text-danger me-1"></i> Gagal memperbarui koordinat.`;
                }
            })
            .catch(err => {
                console.error(err);
                statusElement.innerHTML = `<i class="fa-solid fa-circle-xmark text-danger me-1"></i> Gagal koneksi tracking.`;
            });
    }

    // Simulate location click
    function simulateLocation(lat, lng, label) {
        sendCoordinates(lat, lng, label);
    }

    // Send original browser GPS location
    function shareCurrentLocation() {
        const statusElement = document.getElementById('gps-status');
        if (!navigator.geolocation) {
            statusElement.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Browser tidak mendukung GPS.`;
            return;
        }

        statusElement.innerHTML = `<span class="spinner-border spinner-border-sm text-primary me-1" role="status"></span> Membaca satelit GPS...`;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                sendCoordinates(lat, lng, 'Koordinat Perangkat');
            },
            (error) => {
                console.log(error);
                console.log("Code:", error.code);
                console.log("Message:", error.message);

                let pesan = "";

                switch (error.code) {
                    case 1:
                        pesan = "Permission Denied";
                        break;
                    case 2:
                        pesan = "Position Unavailable";
                        break;
                    case 3:
                        pesan = "Timeout";
                        break;
                    default:
                        pesan = error.message;
                }

                statusElement.innerHTML = pesan;
            }
        );
    }

    // Optional Auto-update loop (comment out if not needed during demo)
    // setInterval(shareCurrentLocation, 10000);
</script>
@endif
@endsection