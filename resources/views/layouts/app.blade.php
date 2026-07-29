<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT Indah Kuala Kapuas') - Sistem Informasi Distribusi BBM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/style.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Leaflet.js Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Custom Styling -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .navbar-brand-custom {
            font-weight: 800;
            color: #0066ff !important; /* Bright Blue */
            letter-spacing: -0.5px;
        }

        .navbar-brand-custom span {
            color: #000000; /* Black */
        }

        .btn-primary-custom {
            background-color: #0066ff; /* Bright Blue */
            border-color: #0066ff;
            color: #ffffff;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary-custom:hover {
            background-color: #0052cc;
            border-color: #0052cc;
            color: #ffffff;
        }

        .btn-warning-custom {
            background-color: #000000; /* Black */
            border-color: #000000;
            color: #ffffff;
            font-weight: 600;
        }

        .btn-warning-custom:hover {
            background-color: #1f2937;
            border-color: #1f2937;
            color: #ffffff;
        }

        .sidebar-card {
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        .nav-link-custom {
            font-weight: 500;
            color: #475569;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            background-color: #eff6ff;
            color: #1e3a8a;
        }

        .stats-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-custom tr {
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
            border-radius: 8px;
        }

        .table-custom th {
            border: none;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 12px 16px;
        }

        .table-custom td {
            border: none;
            padding: 16px;
            vertical-align: middle;
        }

        .table-custom td:first-child, .table-custom th:first-child {
            border-radius: 8px 0 0 8px;
        }

        .table-custom td:last-child, .table-custom th:last-child {
            border-radius: 0 8px 8px 0;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('logo.jpg') }}" alt="Logo PT IKK" style="width: 36px; height: 36px; object-fit: contain; border-radius: 6px;">
                <span>PT. Indah Kuala Kapuas</span>
            </a>
            <button class="navbar-expand-lg navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pesanan.index') }}"><i class="fa-solid fa-receipt me-1"></i> Pesanan</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.pengiriman.index') }}"><i class="fa-solid fa-truck-fast me-1"></i> Pengiriman</a></li>
                        @elseif(Auth::user()->role === 'mitra')
                            <li class="nav-item"><a class="nav-link" href="{{ route('mitra.dashboard') }}"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('mitra.pesanan.index') }}"><i class="fa-solid fa-history me-1"></i> Pesanan Saya</a></li>
                        @elseif(Auth::user()->role === 'driver')
                            <li class="nav-item"><a class="nav-link" href="{{ route('driver.dashboard') }}"><i class="fa-solid fa-truck me-1"></i> Dashboard Driver</a></li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <div class="dropdown">
                            <a class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user fs-5 text-primary"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <span class="badge bg-secondary text-capitalize">{{ Auth::user()->role }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary-custom px-4">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-5">
        <div class="container">
            <!-- Global Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation fs-5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-5 border-top border-secondary">
        <div class="container text-center">
            <p class="mb-1">&copy; {{ date('Y') }} PT Indah Kuala Kapuas. All Rights Reserved.</p>
            <small class="text-secondary">Sistem Informasi Manajemen & Distribusi BBM Berbasis Web - Laravel & Leaflet.js</small>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Leaflet.js Maps JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @yield('scripts')
</body>
</html>
