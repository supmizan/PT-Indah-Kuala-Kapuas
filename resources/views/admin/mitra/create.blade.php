@extends('layouts.dashboard')

@section('title', 'Tambah Mitra Baru')

@section('styles')
<style>
    /* Styling map picker area */
    #map-picker {
        height: 320px;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
        z-index: 1;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Header Halaman -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold mb-0">Tambah Mitra Kerja Baru</h3>
            <a href="{{ route('admin.mitra.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </div>

        <!-- Card Form Tambah Mitra -->
        <div class="card-custom">
            <!-- Alert Error Validasi -->
            @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 shadow-2xs mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form input tambah mitra -->
            <form action="{{ route('admin.mitra.store') }}" method="POST">
                @csrf

                <!-- Seksi Akun Login -->
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-circle-user me-2"></i> Akun Pengguna</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Nama Penanggung Jawab</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Contoh: budi@mitra.com" required>
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="form-label fw-medium">Password Akun</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <!-- Seksi Profil Perusahaan -->
                <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-building me-2"></i> Profil Perusahaan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="nama_perusahaan" class="form-label fw-medium">Nama Perusahaan / Instansi</label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan') }}" placeholder="Contoh: PT Borneo Logistik" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label fw-medium">Nomor HP / Kontak</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="Contoh: 0812345678" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga_per_liter" class="form-label fw-medium">Harga BBM per Liter (Rp)</label>
                        <input type="number" step="0.01" min="0" name="harga_per_liter" id="harga_per_liter" class="form-control" value="{{ old('harga_per_liter', 15000) }}" placeholder="Contoh: 15200" required>
                    </div>
                    <div class="col-md-12">
                        <label for="alamat" class="form-label fw-medium">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3" class="form-control" placeholder="Tuliskan alamat lengkap kantor/gudang mitra..." required>{{ old('alamat') }}</textarea>
                    </div>
                    
                    <!-- Seksi Titik Koordinat GPS + Map Picker (Opsi B) -->
                    <div class="col-md-12 mt-4">
                        <label class="form-label fw-semibold text-primary"><i class="fa-solid fa-map-location-dot me-1"></i> Pilih Lokasi Mitra pada Peta</label>
                        <div id="map-picker" class="mb-2"></div>
                        <p class="small text-muted mb-3"><i class="fa-solid fa-circle-info me-1"></i> Klik pada peta untuk mengisi otomatis form latitude/longitude di bawah, atau ketik secara manual.</p>
                    </div>
                    <div class="col-md-6">
                        <label for="latitude" class="form-label fw-medium">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Contoh: -0.0270" value="{{ old('latitude') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label fw-medium">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Contoh: 109.3500" value="{{ old('longitude') }}">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold text-uppercase shadow-sm">
                    Tambah Mitra Baru <i class="fa-solid fa-plus ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Koordinat awal (default: Kota Pontianak)
        const defaultCoords = [-0.0263, 109.3425];
        const map = L.map('map-picker').setView(defaultCoords, 13);

        // Tile layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        let marker = null;

        // Tampilkan marker awal jika latitude/longitude terisi (misalnya saat reload form karena error validasi)
        const oldLat = document.getElementById('latitude').value;
        const oldLng = document.getElementById('longitude').value;
        if (oldLat && oldLng) {
            const oldCoords = [parseFloat(oldLat), parseFloat(oldLng)];
            marker = L.marker(oldCoords).addTo(map);
            map.setView(oldCoords, 15);
        }

        // Tangani klik pada peta untuk mengambil koordinat
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Masukkan koordinat ke dalam input form
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Geser atau tambahkan marker baru
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });

        // Tangani input manual pada form untuk menggerakkan marker peta secara langsung
        function updateMapFromInputs() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                const newCoords = [lat, lng];
                if (marker) {
                    marker.setLatLng(newCoords);
                } else {
                    marker = L.marker(newCoords).addTo(map);
                }
                map.setView(newCoords, 15);
            }
        }

        document.getElementById('latitude').addEventListener('input', updateMapFromInputs);
        document.getElementById('longitude').addEventListener('input', updateMapFromInputs);
    });
</script>
@endsection