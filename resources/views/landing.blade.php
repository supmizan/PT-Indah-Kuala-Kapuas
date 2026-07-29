@extends('layouts.app')

@section('title', 'Beranda')

@section('styles')
<style>
    /* Premium Org Chart Styling */
    .org-chart-wrapper {
        background-color: #0f172a; /* Slate 900 */
        border-radius: 20px;
        padding: 45px 30px;
        color: #f8fafc;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        overflow-x: auto;
    }

    .org-tree {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 760px; /* Ensures layout doesn't break on small viewports of chart area */
    }

    .org-node {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        padding: 14px 24px;
        text-align: center;
        min-width: 190px;
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        transition: all 0.25s ease-in-out;
    }

    .org-node:hover {
        transform: translateY(-3px);
        border-color: #3b82f6; /* Blue glow */
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
    }

    .org-node-icon {
        width: 32px;
        height: 32px;
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px auto;
        font-size: 0.95rem;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .org-node .name {
        font-weight: 700;
        font-size: 1rem;
        color: #ffffff;
        letter-spacing: 0.5px;
    }

    .org-node .title {
        font-size: 0.75rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 4px;
    }

    /* Vertical lines */
    .line-v {
        width: 2px;
        height: 24px;
        background: linear-gradient(to bottom, #3b82f6, #60a5fa);
    }

    /* 3-column Branch styling */
    .branch-3 {
        display: flex;
        justify-content: space-between;
        width: 100%;
        position: relative;
        padding-top: 24px;
    }

    /* The horizontal connecting line for 3 branches */
    .branch-3::before {
        content: '';
        position: absolute;
        top: 0;
        left: 16.6%;
        right: 16.6%;
        height: 2px;
        background: #3b82f6;
    }

    .branch-col {
        width: 33.33%;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    /* Small vertical line above each column in the split */
    .branch-col::before {
        content: '';
        position: absolute;
        top: -24px;
        width: 2px;
        height: 24px;
        background: #3b82f6;
    }

    /* Driver level branch styling (5 columns) */
    .branch-5 {
        display: flex;
        justify-content: space-between;
        width: 100%;
        position: relative;
        padding-top: 24px;
        margin-top: 10px;
    }

    /* The horizontal connecting line for 5 drivers */
    .branch-5::before {
        content: '';
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #3b82f6;
    }

    .driver-col {
        width: 20%;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .driver-col::before {
        content: '';
        position: absolute;
        top: -24px;
        width: 2px;
        height: 24px;
        background: #3b82f6;
    }

    .driver-node {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 10px 14px;
        text-align: center;
        min-width: 120px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
    }

    .driver-node:hover {
        transform: translateY(-2px);
        border-color: #a855f7;
        box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
    }

    .driver-node i {
        color: #a855f7;
        margin-bottom: 6px;
        font-size: 0.95rem;
    }

    .driver-node .title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #e2e8f0;
    }

    /* Contact Section Styling */
    .contact-info-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
    }
    .contact-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 30px;
    }
    .contact-label {
        font-weight: 700;
        color: #0066ff; /* Bright electric blue */
        margin-bottom: 6px;
        font-size: 1.1rem;
    }
    .contact-value {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    @media (min-width: 992px) {
        .border-lg-end {
            border-right: 1px solid #dee2e6 !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="row align-items-center mb-5 py-4">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3 fw-semibold text-uppercase tracking-wider">Distribusi BBM Resmi & Terpercaya</span>
        <h1 class="display-4 fw-bold mb-3 text-slate-900" style="line-height: 1.15;">
            Sistem Pengangkutan BBM <br><span class="text-primary">PT Indah Kuala Kapuas</span>
        </h1>
        <p class="lead text-secondary mb-4">
            Solusi digital pengiriman Bahan Bakar Minyak (BBM) yang aman, efisien, dan terpantau secara real-time di seluruh wilayah Kalimantan Barat.
        </p>
        <div class="d-flex flex-wrap gap-3">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary-custom btn-lg px-4 shadow"><i class="fa-solid fa-chart-pie me-2"></i>Dashboard Admin</a>
                @elseif(Auth::user()->role === 'mitra')
                    <a href="{{ route('mitra.dashboard') }}" class="btn btn-primary-custom btn-lg px-4 shadow"><i class="fa-solid fa-cart-shopping me-2"></i>Ajukan BBM</a>
                @else
                    <a href="{{ route('driver.dashboard') }}" class="btn btn-primary-custom btn-lg px-4 shadow"><i class="fa-solid fa-road me-2"></i>Dashboard Driver</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary-custom btn-lg px-4 shadow"><i class="fa-solid fa-right-to-bracket me-2"></i>Mulai Sekarang</a>
            @endauth
        </div>
    </div>
    <div class="col-lg-6 text-center">
        <!-- Visual fuel truck image banner -->
        <img src="{{ asset('truck.jpg') }}" alt="Truk Pengangkut BBM PT IKK" class="img-fluid rounded-4 shadow-sm border border-light" style="width: 100%; max-height: 340px; object-fit: cover;">
    </div>
</div>

<!-- Service Statistics Panel (Memanjang secara horizontal) -->
<div class="row mb-5">
    <div class="col-12">
        <div class="p-4 bg-white rounded-4 shadow-sm border text-start">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
                <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-truck-moving me-2 text-primary"></i> Statistik Layanan</h5>
                <small class="text-muted"><i class="fa-solid fa-circle-info me-1 text-primary"></i> Didukung oleh Geolocation API dan Leaflet.js untuk transparansi status.</small>
            </div>
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary h-100">
                        <small class="text-secondary d-block">Cakupan Wilayah</small>
                        <strong class="fs-4 text-dark">10 Kab/Kota</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-dark h-100">
                        <small class="text-secondary d-block">Jumlah Mitra</small>
                        <strong class="fs-4 text-dark">49+ Mitra</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary h-100">
                        <small class="text-secondary d-block">Armada Mobil Tangki</small>
                        <strong class="fs-4 text-dark">Tersedia</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-dark h-100">
                        <small class="text-secondary d-block">Sistem Tracking</small>
                        <strong class="fs-4 text-dark">Real-Time</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visi & Misi Section -->
<div class="row py-5 bg-white rounded-4 border shadow-sm mx-1 mb-5">
    <div class="col-12 text-center mb-5">
        <h2 class="fw-bold">Visi & Misi Perusahaan</h2>
        <div class="mx-auto bg-primary rounded" style="width: 60px; height: 4px;"></div>
    </div>
    <div class="col-lg-6 px-4 mb-4 mb-lg-0 border-lg-end">
        <div class="d-flex gap-3 align-items-start mb-3">
            <div class="p-3 bg-primary-subtle text-primary rounded-3">
                <i class="fa-solid fa-eye fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold text-primary">Visi Kami</h4>
                <p class="text-secondary lead fs-6">
                    Menjadi perusahaan pengangkutan dan distribusi Bahan Bakar Minyak (BBM) terkemuka di wilayah Kalimantan Barat yang unggul dalam keselamatan, keandalan operasional, dan kepuasan mitra kerja.
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-6 px-4">
        <div class="d-flex gap-3 align-items-start mb-3">
            <div class="p-3 bg-warning-subtle text-warning rounded-3">
                <i class="fa-solid fa-bullseye fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold text-warning">Misi Kami</h4>
                <ul class="text-secondary ps-3 fs-6">
                    <li class="mb-2">Menyediakan layanan pengangkutan BBM dengan mengutamakan standar keselamatan kerja yang tinggi (K3).</li>
                    <li class="mb-2">Mengoptimalkan operasional distribusi melalui armada tangki yang terawat serta sistem digital terintegrasi.</li>
                    <li class="mb-2">Membangun kemitraan yang berkelanjutan dengan transparansi informasi pengiriman real-time.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Struktur Perusahaan Section -->
<div class="row py-5 mb-5 align-items-center">
    <div class="col-lg-4 mb-4 mb-lg-0">
        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill mb-2 fw-semibold">STRUKTUR ORGANISASI</span>
        <h2 class="fw-bold mb-3">Struktur Perusahaan PT Indah Kuala Kapuas</h2>
        <p class="text-secondary mb-4">
            PT Indah Kuala Kapuas memiliki struktur manajemen terstruktur untuk memastikan kelancaran administrasi kantor serta keandalan distribusi di lapangan.
        </p>
        <div class="d-flex flex-column gap-3">
            <div class="d-flex gap-3 align-items-center">
                <i class="fa-solid fa-user-tie fs-4 text-primary"></i>
                <div>
                    <strong>Fakrul Ilmi</strong>
                    <p class="text-muted mb-0 small">Komisaris PT Indah Kuala Kapuas</p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <i class="fa-solid fa-user-astronaut fs-4 text-primary"></i>
                <div>
                    <strong>Sanazie</strong>
                    <p class="text-muted mb-0 small">Direktur PT Indah Kuala Kapuas</p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <i class="fa-solid fa-users-gear fs-4 text-primary"></i>
                <div>
                    <strong>Manajemen Khusus</strong>
                    <p class="text-muted mb-0 small">Adrian (Marketing), Aulia (Administrasi), Supriyan Irawan (Kepala Operasional)</p>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <i class="fa-solid fa-truck fs-4 text-primary"></i>
                <div>
                    <strong>4 Supir Tangki Utama (Drivers)</strong>
                    <p class="text-muted mb-0 small">Melaksanakan distribusi BBM ke seluruh wilayah Kalimantan Barat.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Interactive CSS Org Chart -->
        <div class="org-chart-wrapper">
            <div class="org-tree">
                <!-- Tier 1: Komisaris -->
                <div class="org-node">
                    <div class="org-node-icon"><i class="fa-solid fa-user-tie"></i></div>
                    <div class="name">FAKRUL ILMI</div>
                    <div class="title">KOMISARIS</div>
                </div>

                <div class="line-v"></div>

                <!-- Tier 2: Direktur -->
                <div class="org-node">
                    <div class="org-node-icon"><i class="fa-solid fa-user-astronaut"></i></div>
                    <div class="name">SANAZIE</div>
                    <div class="title">DIREKTUR</div>
                </div>

                <div class="line-v"></div>

                <!-- Tier 3: Split into Marketing, Kepala Operasional, Administrasi -->
                <div class="branch-3">
                    <!-- Left: Adrian (Marketing) -->
                    <div class="branch-col">
                        <div class="org-node">
                            <div class="org-node-icon"><i class="fa-solid fa-bullhorn"></i></div>
                            <div class="name">ADRIAN</div>
                            <div class="title">MARKETING</div>
                        </div>
                    </div>

                    <!-- Center: Supriyan Irawan (Kepala Operasional) -->
                    <div class="branch-col">
                        <div class="org-node">
                            <div class="org-node-icon"><i class="fa-solid fa-gears"></i></div>
                            <div class="name">SUPRIYAN IRAWAN</div>
                            <div class="title">KEPALA OPERASIONAL</div>
                        </div>

                        <!-- Connector lines down to drivers -->
                        <div class="line-v"></div>
                        
                        <!-- Tier 4: Drivers split (under Kepala Operasional) -->
                        <div class="branch-5">
                            <div class="driver-col">
                                <div class="driver-node">
                                    <i class="fa-solid fa-steering-wheel fa-truck"></i>
                                    <div class="title">DRIVER</div>
                                </div>
                            </div>
                            <div class="driver-col">
                                <div class="driver-node">
                                    <i class="fa-solid fa-steering-wheel fa-truck"></i>
                                    <div class="title">DRIVER</div>
                                </div>
                            </div>
                            <div class="driver-col">
                                <div class="driver-node">
                                    <i class="fa-solid fa-steering-wheel fa-truck"></i>
                                    <div class="title">DRIVER</div>
                                </div>
                            </div>
                            <div class="driver-col">
                                <div class="driver-node">
                                    <i class="fa-solid fa-steering-wheel fa-truck"></i>
                                    <div class="title">DRIVER</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Aulia (Administrasi) -->
                    <div class="branch-col">
                        <div class="org-node">
                            <div class="org-node-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                            <div class="name">AULIA</div>
                            <div class="title">ADMINISTRASI</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lokasi Distribusi Section -->
<div class="row bg-white py-5 rounded-4 border shadow-sm mx-1">
    <div class="col-12 text-center mb-4">
        <h2 class="fw-bold">Wilayah Cakupan Distribusi</h2>
        <p class="text-secondary">Kami melayani distribusi Bahan Bakar Minyak di 10 Kabupaten/Kota Kalimantan Barat</p>
    </div>
    <div class="col-12">
        <div class="row row-cols-2 row-cols-md-5 g-3 text-center">
            @foreach(['Kota Pontianak', 'Kubu Raya', 'Bengkayang', 'Singkawang', 'Sambas', 'Sanggau', 'Sekadau', 'Sintang', 'Kapuas Hulu (Putussibau)', 'Ketapang'] as $city)
                <div class="col">
                    <div class="p-3 bg-light rounded-3 shadow-2xs border text-slate-800 fw-medium">
                        <i class="fa-solid fa-map-location-dot text-primary me-2"></i>{{ $city }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Hubungi Kami Section -->
<div class="row py-5 mt-5 align-items-stretch" id="hubungi-kami">
    <div class="col-12 text-center mb-5">
        <h2 class="fw-bold mb-2">Hubungi Kami</h2>
        <p class="text-secondary">Silakan hubungi kami apabila membutuhkan informasi mengenai Sistem Monitoring BBM PT Indah Kuala Kapuas.</p>
        <div class="mx-auto bg-primary rounded" style="width: 60px; height: 4px;"></div>
    </div>
    
    <!-- Contact Info Card -->
    <div class="col-lg-5 mb-4 mb-lg-0">
        <div class="contact-info-card h-100">
            <h4 class="contact-title">PT Indah Kuala Kapuas</h4>
            
            <div class="mb-4">
                <div class="contact-label">Alamat</div>
                <div class="contact-value">
                    Jl. Tanjung Harapan Gang Gelora, Desa/Kelurahan Banjar Serasan, Kecamatan Pontianak Timur, Provinsi Kalimantan Barat.
                </div>
            </div>

            <div class="mb-4">
                <div class="contact-label">Email</div>
                <div class="contact-value">
                    admin@monitoringbbm.id
                </div>
            </div>

            <div class="mb-0">
                <div class="contact-label">Telepon</div>
                <div class="contact-value mb-0">
                    (0561) 000000
                </div>
            </div>
        </div>
    </div>
    
    <!-- Leaflet Map Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 p-2 bg-white" style="border: 1px solid rgba(0,0,0,0.05) !important;">
            <div id="contact-map" style="height: 100%; min-height: 380px; border-radius: 12px; z-index: 1;"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Gang Gelora Coordinates (Pontianak Timur)
        const ggGeloraCoords = [-0.0274, 109.3693];
        
        // Initialize map
        const map = L.map('contact-map').setView(ggGeloraCoords, 16);

        // Add OSM Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Custom Marker
        const marker = L.marker(ggGeloraCoords).addTo(map);
        
        marker.bindPopup(`
            <div style="font-family: 'Outfit', sans-serif; font-size: 0.9rem; min-width: 150px;">
                <strong style="color: #0066ff; display: block; margin-bottom: 2px;">PT Indah Kuala Kapuas</strong>
                <span style="color: #64748b; font-size: 0.8rem; line-height: 1.3; display: block;">Gg. Gelora, Banjar Serasan</span>
            </div>
        `).openPopup();
    });
</script>
@endsection
