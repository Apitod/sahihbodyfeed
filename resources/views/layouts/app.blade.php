<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Sahihbodyfeed - @yield('title', 'Sistem Multi-Level')</title>
    <!-- CSS files from Tabler.io -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
            --brand-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        body { font-feature-settings: "cv03", "cv04", "cv11"; }
        .navbar-brand-autodark { font-weight: 800; letter-spacing: -0.02em; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 12px; }
        .btn-primary { background: var(--accent-gradient); border: none; border-radius: 8px; font-weight: 600; }
        .badge { border-radius: 6px; padding: 0.35em 0.65em; font-weight: 600; }
        .navbar-vertical .nav-link { color: rgba(255, 255, 255, 0.7); font-weight: 500; }
        .navbar-vertical .nav-link:hover, .navbar-vertical .nav-link.active { color: #ffffff !important; }
        .navbar-vertical .nav-link-icon { color: rgba(255, 255, 255, 0.5) !important; }
        .navbar-vertical .nav-link:hover .nav-link-icon, .navbar-vertical .nav-link.active .nav-link-icon { color: #ffffff !important; }
        .navbar-vertical .dropdown-item { color: rgba(255, 255, 255, 0.7) !important; }
        .navbar-vertical .dropdown-item:hover { background: rgba(255, 255, 255, 0.1); color: #ffffff !important; }
        .navbar-vertical .dropdown-toggle:after { filter: brightness(0) invert(1); opacity: 0.5; }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page">
        <!-- Vertical Navbar (Sidebar) -->
        @auth
        <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark px-2 py-4">
                    <a href="/" class="text-white text-decoration-none">SAHIHBODYFEED</a>
                </h1>
                
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm bg-blue-lt">{{ substr(auth()->user()->username, 0, 2) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="{{ route('profile') }}" class="dropdown-item">Profil Saya</a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
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
            <header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none bg-white border-bottom py-2">
                <div class="container-fluid">
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm bg-blue-lt">{{ substr(auth()->user()->username, 0, 2) }}</span>
                                <div class="d-none d-xl-block ps-2">
                                    <div class="font-weight-bold">{{ auth()->user()->username }}</div>
                                    <div class="mt-1 small text-secondary">{{ auth()->user()->role->label() }}</div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile') }}" class="dropdown-item">Profil Saya</a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
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
