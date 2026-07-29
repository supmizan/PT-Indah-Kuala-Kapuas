@extends('layouts.dashboard')

@section('title', 'Lacak Pesanan Saya')

@section('styles')
<style>
    #map {
        height: 500px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        z-index: 1;
    }

    .info-item {
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .gray-marker {
        filter: grayscale(100%) brightness(80%);
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Lacak Pengiriman BBM Live</h2>
        <p class="text-secondary mb-0">Pantau pergerakan mobil tangki BBM menuju lokasi perusahaan Anda</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('mitra.pesanan.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
    </div>
</div>

<div class="row g-4">
    <!-- Map Column -->
    <div class="col-lg-8">
        <div class="card-custom p-2">
            <div id="map"></div>
        </div>
    </div>

    <!-- Info Column -->
    <div class="col-lg-4">
        <div class="card-custom">
            <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-truck-moving me-2"></i>Status Pengiriman</h5>

            <div class="info-item">
                <small class="text-muted d-block fw-semibold">Volume BBM Pesanan</small>
                <strong class="fs-5 text-primary">{{ number_format($pengiriman->pesanan->jumlah_bbm) }} Liter</strong>
            </div>

            <div class="info-item">
                <small class="text-muted d-block fw-semibold">Petugas Driver</small>
                <strong>{{ $pengiriman->driver->user->name }}</strong>
                <p class="mb-0 text-secondary small"><i class="fa-solid fa-phone me-1"></i> {{ $pengiriman->driver->no_hp }}</p>
            </div>

            <div class="info-item">
                <small class="text-muted d-block fw-semibold">Armada Mobil Tangki</small>
                <strong>{{ $pengiriman->armada->no_polisi }}</strong>
                <span class="badge bg-secondary text-uppercase">{{ $pengiriman->armada->jenis }}</span>
            </div>

            <div class="info-item">
                <small class="text-muted d-block fw-semibold">Lokasi Tujuan Anda</small>
                <p class="mb-0 text-dark fw-semibold small"><i class="fa-solid fa-map-marker-alt text-danger me-1"></i> {{ $pengiriman->pesanan->mitra->alamat }}</p>
            </div>

            <div class="info-item bg-light p-3 rounded-3 mt-4">
                <h6 class="fw-bold text-dark small mb-1"><i class="fa-solid fa-circle-info text-info me-1"></i>Informasi Pelacakan</h6>
                <p class="mb-0 text-muted small" style="line-height: 1.4;">
                    Peta memuat koordinat real-time dari smartphone supir. Jika mobil tangki bergerak, posisi pada peta akan bergeser otomatis setiap 5 detik.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const officeCoords = [-0.0234, 109.3719]; // PT IKK HQ
        @php
        $mitraLoc = $pengiriman->pesanan->mitra;
        @endphp
        @if($mitraLoc->latitude && $mitraLoc->longitude)
        const destCoords = [{
            {
                $mitraLoc->latitude
            }
        }, {
            {
                $mitraLoc->longitude
            }
        }]; // Lokasi {{ $mitraLoc->nama_perusahaan }}
        @else
        const destCoords = null; // Admin belum mengisi koordinat lokasi mitra ini
        @endif

        // Initialize Map
        const map = L.map('map').setView(officeCoords, 13);

        // Add Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // HQ Marker
        const officeIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/2558/2558832.png',
            iconSize: [36, 36],
            iconAnchor: [18, 36]
        });
        L.marker(officeCoords, {
                icon: officeIcon
            }).addTo(map)
            .bindPopup('<strong>PT Indah Kuala Kapuas (HQ)</strong><br>Titik Muat BBM');

        // Destination Marker (Mitra)
        if (destCoords) {
            const destIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/8065/8065067.png',
                iconSize: [36, 36],
                iconAnchor: [18, 36]
            });
            L.marker(destCoords, {
                    icon: destIcon
                }).addTo(map)
                .bindPopup('<strong>Lokasi Perusahaan Anda</strong><br>Tujuan Serah Terima BBM').openPopup();
        }

        // Truck Icon (Active Delivery)
        const truckIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/1048/1048314.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });

        let truckMarker = null;
        let routeLine = null;
        let historyMarkers = []; // Keep track of old gray markers so we can clear/refresh them

        const grayIcon = L.icon({
            iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41],
            className: 'gray-marker'
        });

        function updateLocation() {
            fetch("{{ route('pengiriman.lokasi-json', $pengiriman->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        // Clear old history markers
                        historyMarkers.forEach(m => map.removeLayer(m));
                        historyMarkers = [];

                        const pathCoords = [officeCoords];

                        // Draw gray markers for all previous coordinates
                        for (let i = 0; i < data.length - 1; i++) {
                            const point = data[i];
                            if (point.latitude && point.longitude) {
                                const lat = parseFloat(point.latitude);
                                const lng = parseFloat(point.longitude);
                                const prevCoords = [lat, lng];
                                pathCoords.push(prevCoords);

                                // Format update date and time
                                const date = new Date(point.waktu || point.created_at);
                                const dateStr = date.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric'
                                });
                                const timeStr = date.toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                });

                                const m = L.marker(prevCoords, {
                                        icon: grayIcon
                                    }).addTo(map)
                                    .bindPopup(`<strong>Lokasi Sebelumnya</strong><br>Waktu Update: ${dateStr} ${timeStr}`);
                                historyMarkers.push(m);
                            }
                        }

                        // Active / Latest coordinate
                        const lastPoint = data[data.length - 1];
                        if (lastPoint.latitude && lastPoint.longitude) {
                            const lastLat = parseFloat(lastPoint.latitude);
                            const lastLng = parseFloat(lastPoint.longitude);
                            const activeCoords = [lastLat, lastLng];
                            pathCoords.push(activeCoords);
                            if (destCoords) {
                                pathCoords.push(destCoords);
                            }

                            // Format active update date and time
                            const activeDate = new Date(lastPoint.waktu || lastPoint.created_at);
                            const activeDateStr = activeDate.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric'
                            });
                            const activeTimeStr = activeDate.toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            if (!truckMarker) {
                                truckMarker = L.marker(activeCoords, {
                                        icon: truckIcon
                                    }).addTo(map)
                                    .bindPopup(`<strong>Armada Tangki BBM</strong><br>Sedang membawa pesanan Anda<br>Update Terakhir: ${activeDateStr} ${activeTimeStr}`).openPopup();
                            } else {
                                truckMarker.setLatLng(activeCoords);
                                truckMarker.getPopup().setContent(`<strong>Armada Tangki BBM</strong><br>Sedang membawa pesanan Anda<br>Update Terakhir: ${activeDateStr} ${activeTimeStr}`);
                            }

                            // Draw path line connecting all updates
                            if (routeLine) {
                                map.removeLayer(routeLine);
                            }
                            routeLine = L.polyline(pathCoords, {
                                color: '#2563eb',
                                weight: 4,
                                dashArray: '6, 6'
                            }).addTo(map);

                            // Adjust bounds to fit all updates
                            const bounds = L.latLngBounds(pathCoords);
                            map.fitBounds(bounds, {
                                padding: [50, 50]
                            });
                        }
                    }
                })
                .catch(err => console.error("Gagal mengambil update lokasi:", err));
        }

        updateLocation();

        @if($pengiriman->status === 'proses')
        setInterval(updateLocation, 5000);
        @endif
    });
</script>
@endsection