<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'VolTruck')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ---------- GLOBAL CSS ---------- */
        body {
            background: linear-gradient(185deg, #0a0a0a 0%, #5f4007 100%);
            min-height: 100vh;
            position: relative;
            /*font-family: 'Oswald', 'Roboto Condensed', 'Montserrat', 'Courier New', monospace;*/
            font-family: 'Share Tech Mono', 'Fira Mono', 'Courier New', monospace;
            font-weight: 450;
            letter-spacing: 0.03em;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://www.transparenttextures.com/patterns/carbon-fiber.png');
            opacity: 0.1;
            pointer-events: none;
            z-index: 0;
        }
        /* Navbar */
       /* .navbar-custom {
            background: rgba(65, 35, 1, 0.2);
            backdrop-filter: blur(10px);
            color:#6c757d;
            
        }
        */
        .navbar-custom {
            background: transparent !important;
            backdrop-filter: none;
            
        }

        .dropdown-menu-dark-custom {
            background-color: #1e1e1e;
            border: 1px solid #ffd700;
            border-radius: 12px;
        }
        .dropdown-menu-dark-custom .dropdown-item {
            color: #f0f0f0;
        }
        .dropdown-menu-dark-custom .dropdown-item:hover {
            background-color: #ffd700;
            color: #000;
        }
        /* Kartu umum */
        .card-voltruck {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-radius: 10px;
            color: #f0f0f0;
            transition: transform 0.2s;
        }
        .card-voltruck:hover {
            border-color: #ffd700;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }
        .card-voltruck .card-header {
            background: rgba(0,0,0,0.6);
            border-bottom: 1px solid #ffd700;
            color: #ffd966;
            font-weight: bold;
            font-size: 0.95rem;
            border-radius: 10px 10px 0 0;
        }
        /* Tombol gradien kuning */
        .btn-voltruck {
            /*background: linear-gradient(90deg, #584001, #5a4d02);*/
            border: 1px;
            border-color: #5f4007;
            border-radius: 15px;
            padding: 9px 20px;
            font-weight: bold;
            color: #fcf807;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .btn-voltruck:hover {
            background: linear-gradient(90deg, #ffaa00, #ffcc33);
            transform: scale(1.02);
            color: #000;
            box-shadow: 0 6px 15px rgba(255, 215, 0, 0.4);
        }
        /* Alert custom */
        .alert-voltruck {
            background-color: rgba(255, 215, 0, 0.15);
            border-left: 5px solid #ffd700;
            color: #ffecb3;
            border-radius: 12px;
        }
        /* Tabel */
        .table-voltruck {
            color: #ddd;
            border-color: #ffd70033;
        }
        .table-voltruck thead th {
            border-bottom: 2px solid #ffd700;
            color: #ffd966;
        }
        .table-voltruck tbody tr:hover {
            background-color: rgba(255, 215, 0, 0.1);
        }
        .badge-role {
            background-color: #ffd700;
            color: #1a1a1a;
            padding: 2px 5px;
            border-radius: 5px;
            font-weight: bold;
        }
        /* Utility */
        .z-index-1 {
            position: relative;
            z-index: 1;
        }
        hr {
            background-color: #ffd700;
            height: 1px;
            opacity: 0.7;
        }
        /* Halaman Login: card khusus */
        .login-card {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 15px rgba(255, 215, 0, 0.2);
        }
        .login-card .form-control {
            background: #1e1e1e;
            border: 1px solid #ffd700;
            color: #fff;
            border-radius: 12px;
        }
        .login-card .form-control:focus {
            background: #2a2a2a;
            border-color: #ffaa00;
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
        }
        .login-icon-box {
            background-color: #6c757d;
            border-radius: 10px;
            padding: 15px;
            display: inline-block;
        }

        .pagination-sm .page-link {
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
        }
        .pagination-sm .page-item:first-child .page-link,
        .pagination-sm .page-item:last-child .page-link {
            padding: 0.25rem 0.6rem;
        }

        .page-link {
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
        }
    </style>
    <!-- STYLE SHOW HITUNG MUATAN TRUCK-->
    <style>
        .info-card {
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            border: 1px solid #ffd700;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 700;
            color: #ffd966;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .info-value {
            font-size: 0.8rem;
            font-weight: 600;
            background-color: #aebbc5;
            padding-left: 0.6rem;
        }
        .truck-icon {
            text-align: center;
            background: #1e1e1e;
            border-radius: 10px;
            padding: 2rem 1rem;
            border: 2px solid #ffd700;
        }
        canvas {
            background: #1a1a1a;
            border-radius: 10px;
            border: 1px solid #ffd700;
            width: 100%;
            height: auto;
        }
    </style>
    <!--SIDEBAR MENGAMBANG STYLE-->
    <style>
        /*============STYLE ICON ========*/
        .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background-color: #3d3903;  /* warna gray */
            border-radius: 50%;
            /* opsional: jika ingin border kuning */
            /* border: 1px solid #ffd700; */
        }

        .icon-circle i {
            font-size: 1.8rem;
            color: #ffd700; /* warna ikon kuning */
        }

        /* Efek hover (opsional) */
        .icon-circle:hover {
            background-color: rgba(255, 215, 0, 0.3);
            transform: scale(1.05);
        }
        /* ===== SIDEBAR MENGAMBANG ===== */
        .floating-sidebar {
            position: fixed;
            top: 5rem;
            left: 1.3rem;
            min-height: 30rem;
            width: 128px;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-radius: 10px;
            box-shadow: 0 25px 40px rgba(0,0,0,0.5);
            padding: 0.5rem;
            z-index: 1050;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        /* Jarak dari tepi website (kiri, atas, bawah) sudah diatur */
        .floating-sidebar .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,215,0,0.3);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 2px;
            padding: 3px 4px;
            margin: 4px 0;
            color: #f0f0f0;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
        }

        .sidebar-item i {
            font-size: 1rem;
            width: 24px;
        }

        .sidebar-item:hover {
            background: #ffd700;
            color: #1a1a1a;
        }

        .logout-btn {
            background: transparent;
            cursor: pointer;
        }

        .sidebar-divider {
            margin: 12px 0;
            border-color: rgba(255,215,0,0.3);
        }

        /* Konten utama desktop: bergeser ke kanan karena sidebar mengambang */
        .main-content {
            margin-left: 145px;   /* sidebar width 280 + jarak 1rem (16px) + padding */
            padding: 1.5rem;
            transition: margin-left 0.3s ease;
        }

        /* Mobile: sidebar tersembunyi di luar layar kiri, konten penuh */
        @media (max-width: 767.98px) {
            .floating-sidebar {
                transform: translateX(-120%);
                top: 0;
                left: 0;
                bottom: 0;
                border-radius: 0;
                margin: 0;
                width: 280px;
            }
            .floating-sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            /* navbar custom mobile */
            .navbar-custom {
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(8px);
                padding: 0.75rem 1rem;
            }
        }

        /* Desktop: sidebar selalu tampil */
        @media (min-width: 768px) {
            .floating-sidebar {
                transform: translateX(0) !important;
            }
        }
    </style>
        <style>
            .navbar-sticky {
                position: sticky;
                top: 0;
                z-index: 1030;
                background: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(10px);
            }
        </style>
    @stack('styles')
</head>
<body>
    @auth
        <!-- Sidebar (Desktop & Mobile Toggle) -->
        <aside id="mainSidebar" class="floating-sidebar">
            <div class="sidebar-header">
                <nav class="navbar navbar-custom d-none d-md-block navbar-sticky">
                    <div class="container-fluid d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-0">
                                <i class="bi bi-person-circle text-warning" style="font-size: 1rem"></i>
                            </div>
                            <div class="user-info text-start">
                                <div class="user-name text-warning fw-bold py-0 mb-0" style="font-size: 0.8rem">{{ Auth::user()->name }}</div>
                                <div class="user-email text-white-50 small mt-0" style="font-size: 0.6rem">Role: {{ Auth::user()->role }}</div>
                            </div>
                        </div>
                    </div>
                </nav>
                <button class="btn-close btn-close-white d-md-none" id="closeSidebar"></button>
            </div>

            <ul class="sidebar-nav">
                <!-- Menu Home (semua role) -->
                <li><a class="sidebar-item" href="{{ url('/') }}"><i class="bi bi-house-door-fill text-danger"></i> Home</a></li>
                
                <!-- Menu Profile (semua role) -->
                <li><a class="sidebar-item" href="{{ route('profile') }}"><i class="bi bi-person-circle text-primary"></i> Profile</a></li>
                <li><a class="sidebar-item" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan text-info opacity-7"></i> Scanner</a></li>
                <li><a class="sidebar-item" href="{{ route('dashboard.user.scanner') }}"><i class="bi bi-qr-code-scan"></i> Histori</a></li>
                <!-- Menu khusus admin (cek role) -->
                @auth
                    @if(Auth::user()->role === 'admin')
                        <li><a class="sidebar-item" href="{{ route('admin.scanners') }}"><i class="bi bi-qr-code-scan"></i> Data</a></li>
                        <li><a class="sidebar-item" href="{{ route('trucks.index') }}"><i class="bi bi-truck-front-fill text-warning"></i> Truck</a></li>
                        <li><a class="sidebar-item" href="{{ route('drivers.index') }}"><i class="bi bi-file-person-fill text-info"></i> Driver</a></li>
                        <li><hr class="sidebar-divider"></li>

                    @endif
                @endauth

                <!-- Menu Logout (semua role) -->
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-form">
                        @csrf
                        <button type="submit" class="sidebar-item logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </aside>
        <!-- Navbar mobile -->
        <nav class="navbar navbar-custom d-md-none navbar-sticky">
            <div class="container-fluid">
                <button class="btn btn-link text-white p-0 border-0" id="toggleSidebar">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <a class="navbar-brand text-warning fw-bold" href="#">
                    <i class="bi bi-truck-front-fill"></i> Vol<span class="text-danger">Truck</span>
                </a>
            </div>
        </nav>

        <!-- Navbar desktop -->
        <nav class="navbar navbar-custom d-none d-md-block navbar-sticky">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold" href="#">
                    <i class="bi bi-truck-front-fill"></i> Vol<span class="text-danger">Truck</span>
                </a>
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Setting</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Konten utama (akan bergeser jika sidebar terbuka di mobile) -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!--NAVIGATION SCRIPT-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('mainSidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            const closeBtn = document.getElementById('closeSidebar');

            function openSidebar() { sidebar.classList.add('show'); }
            function closeSidebar() { sidebar.classList.remove('show'); }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

            // Tutup sidebar saat klik di luar (mobile)
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && sidebar.classList.contains('show')) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        closeSidebar();
                    }
                }
            });
        });
    </script>
</body>
</html>