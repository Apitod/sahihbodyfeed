<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Sahihbodyfeed - @yield('title', 'Sistem Multi-Level')</title>
    <link rel="icon" type="image/png" href="{{ asset('sahihbodyfeed.png') }}">
    <!-- CSS files from Tabler.io -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --brand-1: #FCE7C8; /* Cream */
            --brand-2: #B1C29E; /* Sage Green */
            --brand-3: #FADA7A; /* Mustard Yellow */
            --brand-4: #F0A04B; /* Orange */
            
            /* Override Tabler / Bootstrap Primary */
            --tblr-primary: var(--brand-4);
            --tblr-primary-rgb: 240, 160, 75;
            --accent: var(--brand-4);
            
            --brand-gradient: linear-gradient(135deg, var(--brand-4) 0%, #d48a3a 100%);
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-primary { background-color: var(--brand-4) !important; }
        .text-primary { color: var(--brand-4) !important; }
        body { font-feature-settings: "cv03", "cv04", "cv11"; background-color: #fcfcfc; }
        .navbar-brand-autodark { font-weight: 800; letter-spacing: -0.02em; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05); border-radius: 12px; }
        .btn-primary { 
            background-color: var(--brand-4) !important; 
            border-color: var(--brand-4) !important;
            color: #fff !important;
            border-radius: 8px; font-weight: 600;
        }
        .btn-outline-primary {
            color: var(--brand-4) !important;
            border-color: var(--brand-4) !important;
        }
        .btn-outline-primary:hover {
            background-color: var(--brand-4) !important;
            color: #fff !important;
        }
        .badge { border-radius: 6px; padding: 0.35em 0.65em; font-weight: 600; }
        
        /* Custom Sidebar Styling */
        .navbar-vertical {
            background-color: var(--brand-2) !important;
            border-right: 1px solid rgba(0,0,0,0.05) !important;
        }
        .navbar-vertical .nav-link { 
            color: rgba(255, 255, 255, 0.85) !important; 
            font-weight: 600; 
            border-radius: 8px;
            margin: 2px 12px;
            padding: 10px 12px;
        }
        .navbar-vertical .nav-link:hover { 
            background: rgba(255, 255, 255, 0.1); 
            color: #ffffff !important; 
        }
        .navbar-vertical .nav-link.active { 
            background: var(--brand-4) !important; 
            color: #ffffff !important; 
            box-shadow: 0 4px 12px rgba(240, 160, 75, 0.3);
        }
        .navbar-vertical .nav-link-icon { 
            color: rgba(255, 255, 255, 0.6) !important; 
        }
        .navbar-vertical .nav-link:hover .nav-link-icon, 
        .navbar-vertical .nav-link.active .nav-link-icon { 
            color: #ffffff !important; 
        }
        /* Fix: Only target items in the sidebar, not in actual dropdown menus */
        .navbar-vertical .dropdown-item { 
            color: #1e293b !important; /* Dark text for dropdown items */
            font-weight: 500;
        }
        .navbar-vertical .dropdown-item:hover { 
            background: rgba(0, 0, 0, 0.05) !important; 
            color: var(--brand-4) !important; 
        }
        .navbar-vertical .dropdown-toggle:after { 
            filter: brightness(0) invert(1); 
        }
        .navbar-brand-autodark {
            filter: none !important;
        }

        /* Mobile Sidebar Fix */
        @media (max-width: 991.98px) {
            .navbar-vertical.navbar-expand-lg {
                background-color: var(--brand-2) !important;
                z-index: 1050 !important;
            }
            .navbar-collapse {
                background-color: var(--brand-2) !important;
                margin: 12px;
                border-radius: 20px;
                padding: 1.5rem;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                z-index: 1060 !important;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page">
        <!-- Vertical Navbar (Sidebar) -->
        @auth
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark border-end shadow-sm">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand px-2">
                    <a href="/" class="text-white text-decoration-none fw-bold" style="letter-spacing: 0.05em; font-size: 1.25rem;">
                        <i class="ti ti-leaf me-1"></i> SAHIHBODYFEED
                    </a>
                </h1>
                
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm rounded-pill" style="background: var(--brand-4); color: white;">{{ substr(auth()->user()->username, 0, 2) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="{{ route('profile') }}" class="dropdown-item">Profil Saya</a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-bold">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="sidebar-menu">
                    @if(auth()->user()->isAgent())
                        @include('layouts.agent_sidebar')
                    @elseif(auth()->user()->isAdmin())
                        @include('layouts.admin_sidebar')
                    @endif
                </div>
            </div>
        </aside>
        @endauth

        <div class="page-wrapper">
            <!-- Header for user info -->
            @auth
            <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none border-bottom py-2" style="background-color: var(--brand-1); border-color: rgba(0,0,0,0.05) !important;">
                <div class="container-fluid">
                    <div class="ms-auto d-flex align-items-center gap-3">

                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm rounded-pill shadow-sm" style="background: var(--brand-4); color: white;">{{ substr(auth()->user()->username, 0, 2) }}</span>
                                <div class="d-none d-xl-block ps-2">
                                    <div class="fw-bold" style="color: #1e293b;">{{ auth()->user()->username }}</div>
                                    <div class="mt-1 small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ auth()->user()->role->label() }}</div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile') }}" class="dropdown-item">Pengaturan Profil</a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            @endauth
            @if(session('success'))
            <div class="container-xl mt-3">
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="container-xl mt-3">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8v4" /><path d="M12 16h.01" /></svg>
                        </div>
                        <div>
                            {{ session('error') }}
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="container-xl mt-3">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>
            @endif

            <div class="page-body">
                <div class="container-xl">
                    @yield('content')
                </div>
            </div>

            <footer class="footer footer-transparent d-print-none py-4 border-top mt-auto" style="background-color: var(--brand-1); border-color: rgba(0,0,0,0.05) !important;">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="#" class="link-secondary">Dokumentasi</a></li>
                                <li class="list-inline-item"><a href="#" class="link-secondary">Bantuan</a></li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Copyright &copy; {{ date('Y') }}
                                    <a href="." class="link-secondary fw-bold" style="color: var(--brand-4) !important;">Sahihbodyfeed</a>.
                                    Semua hak dilindungi.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/js/tabler.min.js"></script>
    <script>
        /**
         * Copy text to clipboard and briefly change the button to show feedback.
         * @param {string} text  - The text to copy.
         * @param {HTMLElement} btn - The button that was clicked (for visual feedback).
         */
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(function () {
                const original = btn.innerHTML;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-primary', 'btn-outline-secondary');
                setTimeout(function () {
                    btn.innerHTML = original;
                    btn.classList.remove('btn-success');
                    btn.classList.add(btn.dataset.originalClass || 'btn-outline-primary');
                }, 1800);
            }).catch(function () {
                alert('Gagal menyalin. Silakan salin secara manual.');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
