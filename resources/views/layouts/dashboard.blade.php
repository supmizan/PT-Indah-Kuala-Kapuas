<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT Indah Kuala Kapuas') - Sistem Distribusi BBM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Leaflet.js Maps CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Dashboard Sidebar Custom CSS -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6; /* Light gray background */
            color: #1f2937;
            overflow-x: hidden;
        }

        /* Layout wrapper */
        .wrapper {
            display: flex;
            align-items: stretch;
            width: 100vw;
            min-height: 100vh;
        }

        /* Sidebar styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background-color: #111111; /* Pitch Black */
            color: #94a3b8;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
        }

        /* Logo / Header */
        .sidebar-header {
            padding: 24px 20px;
            background-color: #000000; /* Deep Black */
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #1f2937;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-header .logo-text {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
            margin: 0;
            line-height: 1.2;
            transition: all 0.2s ease-in-out;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            background-color: #ffffff;
            padding: 2px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        #sidebar.collapsed .logo-img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
        }

        /* Navigation List */
        .sidebar-menu {
            padding: 20px 12px;
            list-style: none;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-category {
            font-size: 0.75rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 15px 12px 5px 12px;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-item {
            margin-bottom: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Active highlight */
        .sidebar-item.active .sidebar-link {
            background-color: #0066ff !important; /* Electric Bright Blue */
            color: #ffffff !important;
        }

        .sidebar-item.active .sidebar-link i {
            color: #ffffff !important;
        }

        /* COLLAPSED STATE RULES (Desktop only) */
        @media (min-width: 992px) {
            #sidebar.collapsed {
                min-width: 80px;
                max-width: 80px;
            }

            #sidebar.collapsed .sidebar-header {
                padding: 20px 10px;
                justify-content: center;
            }

            #sidebar.collapsed .sidebar-header .logo-text {
                display: none;
            }

            #sidebar.collapsed .menu-category {
                display: none;
            }

            #sidebar.collapsed .sidebar-link span {
                display: none;
            }

            #sidebar.collapsed .sidebar-link {
                justify-content: center;
                padding: 12px;
                margin: 0 8px;
            }

            #sidebar.collapsed .sidebar-item.active .sidebar-link {
                background-color: #0066ff !important;
                color: #ffffff !important;
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                padding: 0;
                border-radius: 12px;
            }
        }

        /* Main Content container */
        .content-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0; /* Prevents flex items from growing outer container width */
        }

        /* Header / Top navbar */
        .main-header {
            background-color: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .btn-toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #4b5563;
            cursor: pointer;
            padding: 5px;
        }

        .btn-toggle-sidebar:hover {
            color: #111827;
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-name {
            font-weight: 600;
            color: #374151;
        }

        .btn-logout {
            background-color: #ef4444; /* Red logout button */
            color: #ffffff;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .btn-logout:hover {
            background-color: #dc2626;
        }

        /* Page Content section */
        .page-content {
            padding: 35px 30px;
            flex-grow: 1;
        }

        /* Footer styling */
        .main-footer {
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 20px 30px;
            color: #6b7280;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Common component styling */
        .card-custom {
            background-color: #ffffff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title-custom {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 20px;
            color: #111827;
        }

        /* Summary statistics cards */
        .summary-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .summary-card .label {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
        }

        .summary-card .value {
            font-size: 2.25rem;
            font-weight: 700;
            color: #111827;
        }

        /* Responsive sidebar triggers */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -260px;
                position: fixed;
                z-index: 1002; /* Above overlay */
                height: 100vh;
            }

            #sidebar.active {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1001; /* Behind sidebar, above main content */
            }

            .sidebar-overlay.active {
                display: block;
            }

            .btn-close-sidebar {
                display: block !important;
                background: none;
                border: none;
                color: #94a3b8;
                font-size: 1.25rem;
                cursor: pointer;
                padding: 5px;
                transition: color 0.2s;
            }

            .btn-close-sidebar:hover {
                color: #ffffff;
            }
        }

        /* Hide close button on desktop */
        .btn-close-sidebar {
            display: none;
        }
    </style>
    @yield('styles')
</head>
<body>

    <div class="wrapper">
        <!-- Sidebar Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo PT IKK" class="logo-img">
                    <div class="logo-text">Indah Kuala Kapuas</div>
                </div>
                <button type="button" class="btn-close-sidebar" id="closeSidebar" aria-label="Close sidebar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <ul class="sidebar-menu">
                @php
                    $role = Auth::user() ? Auth::user()->role : 'guest';
                    $route = Route::currentRouteName();
                @endphp

                <!-- ADMIN MENU -->
                @if($role === 'admin')
                    <!-- Dashboard -->
                    <li class="sidebar-item {{ Str::contains($route, 'admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- MASTER DATA -->
                    <li class="menu-category">MASTER DATA</li>
                    
                    <li class="sidebar-item {{ Str::contains($route, 'admin.mitra') ? 'active' : '' }}">
                        <a href="{{ route('admin.mitra.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-building"></i>
                            <span>Mitra Kerja</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Str::contains($route, 'admin.armada') ? 'active' : '' }}">
                        <a href="{{ route('admin.armada.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-truck"></i>
                            <span>Armada</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Str::contains($route, 'admin.driver') ? 'active' : '' }}">
                        <a href="{{ route('admin.driver.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-user-group"></i>
                            <span>Driver</span>
                        </a>
                    </li>

                    <!-- TRANSAKSI -->
                    <li class="menu-category">TRANSAKSI</li>
                    <li class="sidebar-item {{ Str::contains($route, 'admin.pesanan') ? 'active' : '' }}">
                        <a href="{{ route('admin.pesanan.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Pesanan BBM</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Str::contains($route, 'admin.pengiriman') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengiriman.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                            <span>Pengiriman</span>
                        </a>
                    </li>

                    <!-- LAPORAN -->
                    <li class="menu-category">LAPORAN</li>
                    <li class="sidebar-item {{ Str::contains($route, 'admin.laporan') ? 'active' : '' }}">
                        <a href="{{ route('admin.laporan.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                <!-- MITRA MENU -->
                @elseif($role === 'mitra')
                    <li class="sidebar-item {{ Str::contains($route, 'mitra.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('mitra.dashboard') }}" class="sidebar-link">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="menu-category">TRANSAKSI</li>
                    <li class="sidebar-item {{ Str::contains($route, 'mitra.pesanan.create') ? 'active' : '' }}">
                        <a href="{{ route('mitra.pesanan.create') }}" class="sidebar-link">
                            <i class="fa-solid fa-cart-plus"></i>
                            <span>Buat Pesanan BBM</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ Str::contains($route, 'mitra.pesanan.index') ? 'active' : '' }}">
                        <a href="{{ route('mitra.pesanan.index') }}" class="sidebar-link">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Pesanan Saya</span>
                        </a>
                    </li>

                <!-- DRIVER MENU -->
                @elseif($role === 'driver')
                    <li class="sidebar-item {{ Str::contains($route, 'driver.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('driver.dashboard') }}" class="sidebar-link">
                            <i class="fa-solid fa-truck-moving"></i>
                            <span>Tugas Pengiriman</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>

        <!-- Page Content Container -->
        <div class="content-container">
            <!-- Header -->
            <header class="main-header">
                <button type="button" id="sidebarCollapse" class="btn-toggle-sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <div class="header-profile">
                    <span class="profile-name">{{ Auth::user() ? Auth::user()->name : 'Mizan' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Content Area -->
            <div class="page-content">
                <!-- Inner Global Alerts -->
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

            <!-- Footer -->
            <footer class="main-footer">
                <span>&copy; {{ date('Y') }} PT Indah Kuala Kapuas. All rights reserved.</span>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        $(document).ready(function () {
            // Sidebar toggle & close actions
            $('#sidebarCollapse, #sidebarOverlay, #closeSidebar').on('click', function () {
                if ($(window).width() >= 992) {
                    $('#sidebar').toggleClass('collapsed');
                } else {
                    $('#sidebar').toggleClass('active');
                    $('#sidebarOverlay').toggleClass('active');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
